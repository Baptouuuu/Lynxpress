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
	
	namespace Admin\Roles\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Admin\Roles\Html\Manage as Html;
	use \Library\Lang\Lang;
	use Exception;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Roles\Roles;
	use \Library\Model\Setting;
	use \Library\Variable\Post as VPost;
	use \Admin\Master\Helpers\Text;
	use \Library\Variable\Get as VGet;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows a user to create or delete a role and update permissions
		*
		* @package		Admin
		* @subpackage	Roles\Controller
		* @author		Baptiste Langlade lynxpressprg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master implements Controller{
	
		private $_roles = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Roles');
			
			if($this->_user->_permissions->setting){
			
				$this->get_roles();
				
				$this->create();
				$this->update();
				$this->delete();
			
			}
		
		}
		
		/**
			* Retrieve all roles
			*
			* @access	private
		*/
		
		private function get_roles(){
		
			try{
			
				$this->_roles = new Roles();
			
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
			* Display roles table
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::actions();
			
			Html::table('o');
			
			$roles = $this->_roles->_roles;
			
			if(!empty($roles))
				foreach($this->_roles->_roles as $r)
					Html::role($r);
			
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
				
				Html::form('o', 'post', Url::_(array('ns' => 'roles')));
				
				$this->display_table();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Create a new role
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::create()){
			
				try{
				
					if(str_word_count(VPost::new_role()) != 1)
						throw new Exception(Lang::_('Role name has to be one word', 'roles'));
					
					$role = new Setting();
					$role->_name = Text::slug(VPost::new_role());
					$role->_type = 'role';
					$role->_data = json_encode($this->_roles->_corpse);
					$role->_key = 'role_'.$role->_name;
					
					//verif if role name already taken
					try{
					
						$already = new Setting($role->_key, '_key');
						
						$id = $already->_id;
						
						if(!empty($id))
							throw new Exception('name taken');
					
					}catch(Exception $e){
					
						if($e->getMessage() == 'name taken')
							throw new Exception(Lang::_('Role name already taken', 'roles'));
					
					}
					
					$role->create();
					
					$this->_roles->refresh();
					
					Activity::log('created the role "'.$role->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::created($result);
			
			}
		
		}
		
		/**
			* Update roles permissions
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update()){
			
				try{
				
					$roles = $this->_roles->_roles;
					
					if(!empty($roles))
						foreach($roles as $role){
						
							if($role->_name == 'admin')
								continue;
							
							$name = 'role_'.$role->_name;
							
							$post = VPost::$name(array());
							
							$corpse = $this->_roles->_corpse;
							
							foreach($corpse as $key => &$bool)
								if(in_array($key, $post))
									$bool = true;
							
							$role->_data = json_encode($corpse);
							$role->update('_data');
						
						}
					
					$this->_roles->refresh();
					
					Activity::log('updated roles permissions');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
		
		/**
			* Delete a role with a check before to see if it's used
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VGet::action() == 'delete' && VGet::id() && $this->_user->_permissions->delete){
			
				try{
				
					$role = new Setting(VGet::id());
					
					$to_read['table'] = 'user';
					$to_read['columns'] = array('_id');
					$to_read['condition_columns'][':r'] = '_role';
					$to_read['condition_select_types'][':r'] = '=';
					$to_read['condition_values'][':r'] = $role->_name;
					$to_read['value_types'][':r'] = 'str';
					
					$users = $this->_db->read($to_read);
					
					if(!empty($users))
						throw new Exception(Lang::_('Can\'t delete role, users are using it', 'roles'));
					
					$role->delete();
					
					$this->_roles->refresh();
					
					Activity::log('deleted the role "'.$role->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VGet::action() == 'delete' && !$this->_user->_permissions->delete){
			
				$this->_action_msg .= ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>