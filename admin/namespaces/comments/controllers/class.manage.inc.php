<?php

	/**
		* @author		Baptiste Langlade
		* @copyright	2011-2012
		* @license		http://www.gnu.org/licenses/gpl.html GNU GPL V3
		* @package		Lynxpress
		*
		* This file is part of Lynxpress.
		*
		*   Lynxpress is free software: you can redistribute it and/or modify
		*   it under the terms of the GNU General Public License as published by
		*   the Free Software Foundation, either version 3 of the License, or
		*   (at your option) any later version.
		*
		*   Lynxpress is distributed in the hope that it will be useful,
		*   but WITHOUT ANY WARRANTY; without even the implied warranty of
		*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		*   GNU General Public License for more details.
		*
		*   You should have received a copy of the GNU General Public License
		*   along with Lynxpress.  If not, see http://www.gnu.org/licenses/.
	*/
	
	namespace Admin\Comments\Controllers;
	use \Admin\Master\Controllers\Controller;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Lang\Lang;
	use \Admin\Comments\Html\Manage as Html;
	use \Admin\Master\Helpers\Html as Helper;
	use Exception;
	use \Library\Database\Database;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Request as VRequest;
	use \Library\Model\Post;
	use \Library\Model\Media;
	use stdClass;
	use \Library\Model\Comment;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Manage comments for your posts and albums
		*
		* @package		Admin
		* @subpackage	Comments\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Controller{
	
		private $_comments = null;
		private $_status = null;
		private $_statuses = null;
		const ITEMS = 5;
		private $_page = null;
		private $_limit_start = null;
		private $_max = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Comments');
			
			if($this->_user->_permissions->comment){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.table.js');
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.button_confirm.js');
				
				if(in_array(VGet::status(), array('pending', 'approved', 'spam', 'trash')))
					$this->_status = VGet::status();
				else
					$this->_status = 'pending';
				
				$this->update();
				$this->delete();
				
				$this->get_comments();
				$this->get_statuses();
			
			}
		
		}
		
		/**
			* Retrieve comments depending of wished status
			*
			* @access	private
		*/
		
		private function get_comments(){
		
			try{
			
				$to_read['table'] = 'comment';
				$to_read['columns'] = array('*');
				$to_read['condition_columns'][':s'] = '_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = $this->_status;
				$to_read['value_types'][':s'] = 'str';
				
				if((VPost::search_button() && VPost::search()) || VGet::search()){
				
					$search = '%'.trim(VRequest::search()).'%';
					
					$to_read['condition_columns']['group'][':n'] = '_name';
					$to_read['condition_select_types'][':n'] = 'LIKE';
					$to_read['condition_values'][':n'] = $search;
					$to_read['value_types'][':n'] = 'str';
					$to_read['condition_types'][':e'] = 'OR';
					$to_read['condition_columns']['group'][':e'] = '_email';
					$to_read['condition_select_types'][':e'] = 'LIKE';
					$to_read['condition_values'][':e'] = $search;
					$to_read['value_types'][':e'] = 'str';
					$to_read['condition_types'][':c'] = 'OR';
					$to_read['condition_columns']['group'][':c'] = '_content';
					$to_read['condition_select_types'][':c'] = 'LIKE';
					$to_read['condition_values'][':c'] = $search;
					$to_read['value_types'][':c'] = 'str';
				
				}elseif(VGet::rel_type() && VGet::rel_id()){
				
					$to_read['condition_types'][':rt'] = 'AND';
					$to_read['condition_columns'][':rt'] = '_rel_type';
					$to_read['condition_select_types'][':rt'] = '=';
					$to_read['condition_values'][':rt'] = VGet::rel_type();
					$to_read['value_types'][':rt'] = 'str';
					$to_read['condition_types'][':ri'] = 'AND';
					$to_read['condition_columns'][':ri'] = '_rel_id';
					$to_read['condition_select_types'][':ri'] = '=';
					$to_read['condition_values'][':ri'] = VGet::rel_id();
					$to_read['value_types'][':ri'] = 'int';
				
				}
				
				//pass $to_read by parameter to have same conditions
				$this->get_pagination($to_read);
				
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array($this->_limit_start, self::ITEMS);
				
				$this->_comments = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Comment');
				
				if(!empty($this->_comments))
					foreach($this->_comments as &$c){
					
						$type = $c->_rel_type;
						
						if($type == 'post'){
						
							$p = new Post();
							$p->_id = $c->_rel_id;
							$p->read('_title');
							$p->read('_permalink');
							
							$rel = new stdClass();
							$rel->_id = $p->_id;
							$rel->_type = $type;
							$rel->_name = $p->_title;
							$rel->_permalink = Url::_(array('ns' => 'posts', 'ctl' => 'view', 'id' => $p->_permalink), array(), true);
							
							$c->_rel = $rel;
						
						}elseif($type == 'media'){
						
							$a = new Media();
							$a->_id = $c->_rel_id;
							$a->read('_name');
							
							$rel = new stdClass();
							$rel->_id = $a->_id;
							$rel->_type = $type;
							$rel->_name = $a->_name;
							$rel->_permalink = Url::_(array('ns' => 'albums', 'ctl' => 'view', 'id' => $a->_id), array(), true);
							
							$c->_rel = $rel;
						
						}
					
					}
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Get pagination informations
			*
			* @access	private
			* @param	array [$to_read]
		*/
		
		private function get_pagination($to_read){
		
			try{
			
				list($this->_page, $this->_limit_start) = Helper::pagination(self::ITEMS);
				
				$to_read['columns'] = array('COUNT(_id) as count');
				
				$count = $this->_db->read($to_read);
				
				$this->_max = ceil($count[0]['count']/self::ITEMS);
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve all statuses of all comments 
			*
			* @access	private
		*/
		
		private function get_statuses(){
		
			try{
			
				$to_read['table'] = 'comment';
				$to_read['columns'] = array('_status', 'COUNT(_status) as count');
				$to_read['groupby'] = '_status';
				
				$s = $this->_db->read($to_read);
				
				$statuses = array('pending' => 0, 'approved' => 0, 'spam' => 0, 'trash' => 0);
				
				foreach($statuses as $key => &$stat)
					if(!empty($s))
						foreach($s as $e)
							if($e['_status'] == $key)
								$stat = $e['count'];
				
				$this->_statuses = $statuses;
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu();
		
		}
		
		/**
			* Display actions for comments
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			Html::submenu('o');
			
			foreach($this->_statuses as $t => $count){
			
				$selected = false;
				
				if($t == $this->_status)
					$selected = true;
				
				Html::status($t, $t, $count, $selected);
			
			}
			
			Html::submenu('c');
			
			Html::actions('o');
			
			switch($this->_status){
			
				case 'pending':
					Html::option('approved', Lang::_('Approve'));
					Html::option('spam', Lang::_('Mark as Spam', 'comments'));
					Html::option('trash', Lang::_('Move to Trash'));
					break;
				
				case 'approved':
					Html::option('pending', Lang::_('Unapprove', 'comments'));
					Html::option('spam', Lang::_('Mark as Spam', 'comments'));
					Html::option('trash', Lang::_('Move to Trash'));
					break;
				
				case 'spam':
					Html::option('approved', Lang::_('Approve'));
					Html::option('pending', Lang::_('Not Spam', 'comments'));
					Html::option('delete', Lang::_('Delete'));
					break;
				
				case 'trash':
					Html::option('pending', Lang::_('Restore'));
					Html::option('delete', Lang::_('Delete'));
					break;
			
			}
			
			Html::actions('c');
		
		}
		
		/**
			* Display buttons to empty trash or spams
			*
			* @access	private
		*/
		
		private function display_empty(){
		
			if($this->_status == 'spam')
				Html::empty_spam();
			elseif($this->_status == 'trash')
				Html::empty_trash();
		
		}
		
		/**
			* Display comments table
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::table('o');
			
			if(!empty($this->_comments))
				foreach($this->_comments as $c)
					Html::comment(
						$c->_id,
						$c->_name,
						$c->_email,
						$c->_content,
						$c->_status,
						$c->_date,
						$c->_rel
					);
			else
				Html::no_comment();
			
			Html::table('c');
		
		}
		
		/**
			* Display pagination
			*
			* @access	private
		*/
		
		private function display_pagination(){
		
			if($this->_max > 1){
			
				$link = array();
				
				if(VRequest::search())
					$link = array('search' => trim(VRequest::search()), 'status' => $this->_status);
				elseif(VGet::rel_type() && VGet::rel_id())
					$link = array('rel_type' => VGet::rel_type(), 'rel_id' => VGet::rel_id(), 'status' => $this->_status);
				else
					$link = array('status' => $this->_status);
				
				Html::pagination($this->_page, $this->_max, $link);
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			Html::noscript();
			
			if($this->_user->_permissions->comment){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'comments'), array('status' => $this->_status)));
				
				$this->display_actions();
				
				$this->display_table();
				
				$this->display_empty();
				$this->display_pagination();
				
				//create a datalist for search input
				echo Helper::datalist('titles', $this->_comments, '_name');
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update status of comments
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::apply() && in_array(VPost::action(), array('pending', 'approved', 'spam', 'trash')) && VPost::comment_id()){
			
				try{
				
					foreach(VPost::comment_id() as $id){
					
						$c = new Comment();
						$c->_id = $id;
						$c->read('_name');
						$c->_status = VPost::action();
						$c->update('_status');
						
						Activity::log('updated a comment of "'.$c->_name.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}elseif(VGet::action() == 'update' && in_array(VGet::to(), array('pending', 'approved', 'spam', 'trash')) && VGet::id()){
			
				try{
				
					$c = new Comment();
					$c->_id = VGet::id();
					$c->read('_name');
					$c->_status = VGet::to();
					$c->update('_status');
					
					Activity::log('updated a comment of "'.$c->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
		
		/**
			* Delete comments
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::apply() && VPost::action() == 'delete' && VPost::comment_id() && $this->_user->_permissions->delete){
			
				try{
				
					foreach(VPost::comment_id() as $id){
					
						$c = new Comment();
						$c->_id = $id;
						$c->read('_name');
						$c->delete();
						
						Activity::log('deleted a comment of "'.$c->_name.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VGet::action() == 'delete' && VGet::id() && $this->_user->_permissions->delete){
			
				try{
				
					$c = new Comment();
					$c->_id = VGet::id();
					$c->read('_name');
					$c->delete();
					
					Activity::log('deleted a comment of "'.$c->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VPost::empty_spam() && $this->_user->_permissions->delete){
			
				try{
				
					$to_read['table'] = 'comment';
					$to_read['columns'] = array('_id', '_name');
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['condition_select_types'][':s'] = '=';
					$to_read['condition_values'][':s'] = 'spam';
					$to_read['value_types'][':s'] = 'str';
					
					$comments = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Comment');
					
					if(!empty($comments))
						foreach($comments as $c){
						
							$c->delete();
							
							Activity::log('deleted a comment of "'.$c->_name.'"');
						
						}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VPost::empty_trash() && $this->_user->_permissions->delete){
			
				try{
				
					$to_read['table'] = 'comment';
					$to_read['columns'] = array('_id', '_name');
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['condition_select_types'][':s'] = '=';
					$to_read['condition_values'][':s'] = 'trash';
					$to_read['value_types'][':s'] = 'str';
					
					$comments = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Comment');
					
					if(!empty($comments))
						foreach($comments as $c){
						
							$c->delete();
							
							Activity::log('deleted a comment of "'.$c->_name.'"');
						
						}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(((VPost::apply() && VPost::action() == 'delete') || (VGet::action() == 'delete' && VGet::id()) || VPost::empty_spam() || VPost::empty_trash()) && !$this->_user->_permissions->delete){
			
				$this->_action_msg .= ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>