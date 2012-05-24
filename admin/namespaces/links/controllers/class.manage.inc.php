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
	
	namespace Admin\Links\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang;
	use Exception;
	use \Admin\Links\Html\Manage as Html;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Database\Database;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Variable\Post as VPost;
	use \Library\Model\Link;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Variable\Get as VGet;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allow users to manage website external links
		*
		* @package		Admin
		* @subpackage	Links\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master implements Controller{
	
		private $_links = null;
		private $_priorites = array();
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Links');
			
			if($this->_user->_permissions->setting){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/table.js');
				
				$this->_priorities = array(
					1 => Lang::_('Very High', 'links'),
					2 => Lang::_('High', 'links'),
					3 => Lang::_('Normal', 'links'),
					4 => Lang::_('Low', 'links'),
					5 => Lang::_('Very Low', 'links')
				);
				
				$this->update();
				$this->delete();
				
				$this->get_links();
			
			}
		
		}
		
		/**
			* Retrieve all links
			*
			* @access	private
		*/
		
		private function get_links(){
		
			try{
			
				$to_read['table'] = 'link';
				$to_read['columns'] = array('*');
				
				if(VPost::search_button() && VPost::search()){
				
					$search = '%'.trim(VPost::search()).'%';
				
					$to_read['condition_columns']['group'][':n'] = '_name';
					$to_read['condition_select_types'][':n'] = 'LIKE';
					$to_read['condition_values'][':n'] = $search;
					$to_read['value_types'][':n'] = 'str';
					$to_read['condition_types'][':l'] = 'OR';
					$to_read['condition_columns']['group'][':l'] = '_link';
					$to_read['condition_select_types'][':l'] = 'LIKE';
					$to_read['condition_values'][':l'] = $search;
					$to_read['value_types'][':l'] = 'str';
				
				}
				
				$to_read['order'] = array('_priority', 'ASC');
				
				$this->_links = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Link');
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
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
			* Display links table with available actions
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::actions();
			
			Html::table('o');
			
			if(!empty($this->_links))
				foreach($this->_links as $l)
					Html::link(
						$l->_id,
						$l->_name,
						$l->_link,
						$l->_rss,
						$l->_notes,
						$this->_priorities[$l->_priority]
					);
			else
				Html::no_link();
			
			Html::table('c');
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			Html::noscript();
			
			if($this->_user->_permissions->setting){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'links')));
				
				$this->display_table();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update priorities to links
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::apply() && VPost::priority() != 'no' && VPost::link_id()){
			
				try{
				
					foreach(VPost::link_id() as $id){
					
						$l = new Link($id);
						$l->_priority = VPost::priority();
						$l->update('_priority');
						
						Activity::log('updated the priority of the link "'.$l->_name.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
		
		/**
			* Delete links from database
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete() && VPost::link_id() && $this->_user->_permissions->delete){
			
				try{
				
					foreach(VPost::link_id() as $id){
					
						$l = new Link($id);
						$l->delete();
						
						Activity::log('deleted the link "'.$l->_name.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::deleted($result);
			
			}elseif(VGet::action() == 'delete' && VGet::id() && $this->_user->_permissions->delete){
			
				try{
				
					$l = new Link(VGet::id());
					$l->delete();
					
					Activity::log('deleted the link "'.$l->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::deleted($result);
			
			}elseif((VGet::action() == 'delete' || VPost::delete()) && !$this->_user->_permissions->delete){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>