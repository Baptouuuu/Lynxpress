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
	
	namespace Admin\Dashboard\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Admin\Dashboard\Html\Manage as Html;
	use \Admin\ActionMessages\ActionMessages;
	use Exception;
	use \Library\Model\User;
	use \Library\Database\Database;
	use \Admin\Categories\Helpers\Categories;
	use \Library\Lang\Lang;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Url\Url;
	use \Admin\Update\Helpers\Update;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Dashboard controller
		*
		* @package		Admin
		* @subpackage	Dashboard\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master implements Controller{
	
		private $_activity = null;
		private $_comments = null;
		private $_drafts = null;
		private $_categories = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Dashboard');
			
			if($this->_user->_permissions->dashboard){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/dashboard.js');
				
				$this->get_activity();
				$this->get_comments();
				$this->get_drafts();
				$this->get_categories();
				
				$this->check_update();
			
			}
		
		}
		
		/**
			* Retrieve last 20 entries oc activity
			*
			* @access	private
		*/
		
		private function get_activity(){
		
			if($this->_user->_permissions->setting){
			
				try{
				
					$to_read['table'] = 'activity';
					$to_read['columns'] = array('*');
					$to_read['order'] = array('_date', 'DESC');
					$to_read['limit'] = array(0, 10);
					
					$this->_activity = $this->_db->read($to_read);
					
					if(!empty($this->_activity))
						foreach($this->_activity as &$a){
						
							$user = new User();
							$user->_id = $a['user_id'];
							$user->read('_username');
							
							$a['username'] = $user->_username;
						
						}
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Retrieve last 5 pending comments
			*
			* @access	private
		*/
		
		private function get_comments(){
		
			if($this->_user->_permissions->comment){
			
				try{
				
					$to_read['table'] = 'comment';
					$to_read['columns'] = array('_id', '_name', '_content', '_rel_id', '_rel_type');
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['condition_select_types'][':s'] = '=';
					$to_read['condition_values'][':s'] = 'pending';
					$to_read['value_types'][':s'] = 'str';
					$to_read['order'] = array('_date', 'DESC');
					$to_read['limit'] = array(0, 5);
					
					$this->_comments = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Comment');
					
					if(!empty($this->_comments))
						foreach($this->_comments as &$c){
						
							$class = '\\Library\\Model\\'.ucfirst($c->_rel_type);
							$model = new $class();
							$model->_id = $c->_rel_id;
							$model->read('_permalink');
							
							if($c->_rel_type == 'post'){
							
								$model->read('_title');
								$model->read('_permalink');
								$model->_rel_name = $model->_title;
								$model->_link = Url::_(array('ns' => 'post', 'id' => $model->_permalink), array(), true);
							
							}elseif($c->_rel_type == 'media'){
							
								$model->read('_name');
								$model->_rel_name = $model->_name;
								$model->_link = Url::_(array('ns' => 'albums', 'id' => $model->_id), array(), true);
							
							}
							
							$c->_rel = $model;
						
						}
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Retrieve last 5 draft posts
			*
			* @access	private
		*/
		
		private function get_drafts(){
		
			if($this->_user->_permissions->post){
			
				try{
				
					$to_read['table'] = 'post';
					$to_read['columns'] = array('_id', '_title', '_content', '_date');
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['condition_select_types'][':s'] = '=';
					$to_read['condition_values'][':s'] = 'draft';
					$to_read['value_types'][':s'] = 'str';
					$to_read['order'] = array('_date', 'DESC');
					$to_read['limit'] = array(0, 5);
					
					$this->_drafts = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Post');
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Retrieve posts categories
			*
			* @access	private
		*/
		
		private function get_categories(){
		
			$this->_categories = Categories::get_type();
		
		}
		
		/**
			* Check if there's a system update available
			*
			* @access	private
		*/
		
		private function check_update(){
		
			try{
			
				$available = Update::check();
				
				if($available === true)
					$this->_action_msg = ActionMessages::update_available(true, true);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Display page title
			*
			* @access	private
		*/
		
		private function display_title(){
		
			Html::menu();
		
		}
		
		/**
			* Display activity widget
			*
			* @access	private
		*/
		
		private function display_activity(){
		
			if($this->_user->_permissions->setting){
			
				Html::widget_activity('o');
				
				if(!empty($this->_activity))
					foreach($this->_activity as $a)
						Html::activity($a['username'], $a['_data'], $a['_date']);
				else
					Html::no_activity();
				
				Html::widget_activity('c');
			
			}
		
		}
		
		/**
			* Display comments widget
			*
			* @access	private
		*/
		
		private function display_comments(){
		
			if($this->_user->_permissions->comment){
			
				Html::widget_comments('o');
				
				if(!empty($this->_comments))
					foreach($this->_comments as $c)
						Html::comment($c->_id, $c->_name, $c->_content, $c->_rel);
				else
					Html::no_comment();
				
				Html::widget_comments('c');
			
			}
		
		}
		
		/**
			* Display post form widget
			*
			* @access	private
		*/
		
		private function display_post(){
		
			if($this->_user->_permissions->post){
			
				Html::widget_post('o');
				
				if(!empty($this->_categories))
					foreach($this->_categories as $c)
						Html::category($c->_id, $c->_name);
				
				Html::widget_post('c');
			
			}
		
		}
		
		/**
			* Display drafts widget
			*
			* @access	private
		*/
		
		private function display_drafts(){
		
			if($this->_user->_permissions->post){
			
				Html::widget_drafts('o');
				
				if(!empty($this->_drafts))
					foreach($this->_drafts as $d)
						Html::draft($d->_id, $d->_title, $d->_content, $d->_date);
				else
					Html::no_draft();
				
				Html::widget_drafts('c');
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_title();
			
			Html::noscript();
			
			if($this->_user->_permissions->dashboard){
			
				echo $this->_action_msg;
				
				Html::dashboard('o');
				
				$this->display_activity();
				$this->display_comments();
				
				Html::dashboard('m');
				
				$this->display_post();
				$this->display_drafts();
				
				Html::dashboard('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
	
	}

?>