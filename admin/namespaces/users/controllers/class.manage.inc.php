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
	
	namespace Admin\Users\Controllers;
	use \Admin\Master\Controllers\Controller;
	use \Admin\Users\Html\Manage as Html;
	use \Library\Lang\Lang;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Master\Helpers\Html as Helper;
	use Exception;
	use \Library\Database\Database;
	use \Admin\Roles\Roles;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Request as VRequest;
	use \Library\Model\User;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Manage website users
		*
		* @package		Admin
		* @subpackage	Users\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Controller{
	
		private $_users = null;
		private $_role = null;
		private $_roles = null;
		private $_user_roles = null;
		const ITEMS = 20;
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
			
			$this->_title = Lang::_('Users');
			
			if($this->_user->_permissions->setting){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.labels.js');
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.button_confirm.js');
				
				$roles = new Roles();
				$this->_roles = $roles->_roles;
				
				if(VGet::role() && isset($this->_roles[VGet::role()]))
					$this->_role = VGet::role();
				else
					$this->_role = 'all';
				
				$this->update();
				$this->delete();
				
				$this->get_users();
				$this->get_user_roles();
			
			}
		
		}
		
		/**
			* Retrieve users
			*
			* @access	private
		*/
		
		private function get_users(){
		
			try{
			
				$to_read['table'] = 'user';
				$to_read['columns'] = array('_id', '_username', '_publicname', '_email', '_role');
				
				if((VPost::search_button() && VPost::search()) || VGet::search()){
				
					$search = '%'.VRequest::search().'%';
				
					$to_read['condition_columns']['group'][':u'] = '_username';
					$to_read['condition_select_types'][':u'] = 'LIKE';
					$to_read['condition_values'][':u'] = $search;
					$to_read['value_types'][':u'] = 'str';
					$to_read['condition_types'][':p'] = 'OR';
					$to_read['condition_columns']['group'][':p'] = '_publicname';
					$to_read['condition_select_types'][':p'] = 'LIKE';
					$to_read['condition_values'][':p'] = $search;
					$to_read['value_types'][':p'] = 'str';
					$to_read['condition_types'][':e'] = 'OR';
					$to_read['condition_columns']['group'][':e'] = '_email';
					$to_read['condition_select_types'][':e'] = 'LIKE';
					$to_read['condition_values'][':e'] = $search;
					$to_read['value_types'][':e'] = 'str';
				
				}
				
				if($this->_role != 'all'){
				
					$to_read['condition_types'][':r'] = 'AND';
					$to_read['condition_columns'][':r'] = '_role';
					$to_read['condition_select_types'][':r'] = '=';
					$to_read['condition_values'][':r'] = $this->_role;
					$to_read['value_types'][':r'] = 'str';
				
				}
				
				$to_read['condition_types'][':a'] = 'AND';
				$to_read['condition_columns'][':a'] = '_active';
				$to_read['condition_select_types'][':a'] = '=';
				$to_read['condition_values'][':a'] = 1;
				$to_read['value_types'][':a'] = 'int';
				
				//pass $to_read by parameter to have same conditions
				$this->get_pagination($to_read);
				
				$to_read['limit'] = array($this->_limit_start, self::ITEMS);
				
				$this->_users = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\User');
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve roles used by users
			*
			* @access	private
		*/
		
		private function get_user_roles(){
		
			try{
			
				$to_read['table'] = 'user';
				$to_read['columns'] = array('_role', 'count(_role) as count');
				$to_read['condition_columns'][':a'] = '_active';
				$to_read['condition_select_types'][':a'] = '=';
				$to_read['condition_values'][':a'] = 1;
				$to_read['value_types'][':a'] = 'int';
				$to_read['groupby'] = '_role';
				
				$this->_user_roles = $this->_db->read($to_read);
			
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
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu();
		
		}
		
		/**
			* Display users actions
			*
			* @acces	private
		*/
		
		private function display_actions(){
		
			Html::submenu('o');
			
			$count_all = 0;
			
			foreach($this->_user_roles as $r)
				$count_all += $r['count'];
			
			Html::role('all', $count_all, (($this->_role == 'all')?true:false));
			
			foreach($this->_user_roles as $r)
				Html::role($r['_role'], $r['count'], (($this->_role == $r['_role'])?true:false));
			
			Html::submenu('c');
			
			Html::actions('o');
			
			foreach($this->_roles as $r)
				Html::option($r->_name, ucfirst($r->_name));
			
			Html::actions('c');
		
		}
		
		/**
			* Display users labels
			*
			* @access	private
		*/
		
		private function display_users(){
		
			Html::users('o');
			
			if(!empty($this->_users))
				foreach($this->_users as $u)
					Html::user(
						$u->_id,
						$u->_username,
						$u->_publicname,
						$u->_email,
						$u->_role,
						$this->_user->_id
					);
			else
				Html::no_user();
			
			Html::users('c');
		
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
					$link = array('search' => trim(VRequest::search()), 'role' => $this->_role);
				else
					$link = array('role' => $this->_role);
				
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
			
			if($this->_user->_permissions->setting){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'users'), array('role' => $this->_role)));
				
				$this->display_actions();
				
				$this->display_users();
				
				$this->display_pagination();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update users roles
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::apply() && VPost::user_id() && VPost::change() != 'no'){
			
				try{
				
					foreach(VPost::user_id() as $id){
					
						$u = new User();
						$u->_id = $id;
						$u->read('_username');
						$u->_role = VPost::change();
						$u->update('_role');
						
						Activity::log('changed the role of "'.$u->_username.'" to '.$u->_role);
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
		
		/**
			* Delete users
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete() && VPost::user_id() && $this->_user->_permissions->delete){
			
				try{
				
					foreach(VPost::user_id() as $id){
					
						$u = new User();
						$u->_id = $id;
						$u->_active = 0;
						$u->update('_active');
						
						Activity::log('deleted the user "'.$u->_username.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VPost::delete() && !$this->_user->_permissions->delete){
			
				$this->_action_msg .= ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>