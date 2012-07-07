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
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang;
	use \Admin\Users\Html\Add as Html;
	use \Library\Model\User;
	use Exception;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Roles\Roles;
	use \Library\Variable\Post as VPost;
	use \Admin\Users\Helpers\User as HUser;
	use \Library\Mail\Mail;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allow the user to add another user
		*
		* @package		Admin
		* @subpackage	Users\Conttrollers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Add extends Master implements Controller{
	
		private $_profile = null;
		private $_roles = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Add User', 'users');
			
			if($this->_user->_permissions->setting){
			
				$this->_profile = new User();
				$this->get_roles();
				
				$this->create();
			
			}
		
		}
		
		/**
			* retrieve all available roles
			*
			* @access	private
		*/
		
		private function get_roles(){
		
			try{
			
				$roles = new Roles();
				$this->_roles = $roles->_roles;
			
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
			* Display user creation form
			*
			* @access	private
		*/
		
		private function display_form(){
		
			Html::user(
				$this->_profile->_username,
				$this->_profile->_email,
				$this->_profile->_role,
				$this->_roles
			);
		
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
				
				Html::form('o', 'post', Url::_(array('ns' => 'users', 'ctl' => 'add')));
				
				$this->display_form();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Check id data are valid ones
			*
			* @access	private
			* @return	boolean
		*/
		
		private function check_data(){
		
			$errors = array();
			
			if($this->_profile->__set('_username', VPost::username()) !== true)
				$errors[] = $this->_profile->__set('_username', VPost::username());
			else
				$this->_profile->_username = VPost::username();
			
			if($this->_profile->__set('_email', VPost::email()) !== true)
				$errors[] = $this->_profile->__set('_email', VPost::email());
			else
				$this->_profile->_email = VPost::email();
			
			if(VPost::pwd() != VPost::re_pwd())
				$errors[] = Lang::_('Passwords doesn\'t match', 'users');
			elseif(in_array(VPost::pwd(), HUser::invalid_passwords()))
				$errors[] = Lang::_('Password two weak', 'users');
			else
				$this->_profile->_password = VPost::pwd();
			
			if(!isset($this->_roles[VPost::role()]))
				$errors[] = Lang::_('Role "%role" do not exist', 'roles', array('role' => VPost::role()));
			else
				$this->_profile->_role = VPost::role();
			
			if(!empty($errors)){
			
				$this->_action_msg .= ActionMessages::errors($errors);
				return false;
			
			}else{
			
				return true;
			
			}
		
		}
		
		/**
			* Create a new user and redirect to manage controller on success
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::create() && $this->check_data()){
			
				try{
				
					//verif if a user already use chosen username
					try{
					
						$user = new User($this->_profile->_username, '_username');
						$id = $user->_id;
						
						if(!empty($id))
							throw new Exception(Lang::_('Username already taken', 'users'));
					
					}catch(Exception $e){
					
						if($e->getMessage() == Lang::_('Username already taken', 'users'))
							throw new Exception(Lang::_('Username already taken', 'users'));
					
					}
					
					//verif if a user already use chosen email
					try{
					
						$user = new User($this->_profile->_email, '_email');
						$id = $user->_id;
						
						if(!empty($id))
							throw new Exception(Lang::_('E-mail already taken', 'users'));
					
					}catch(Exception $e){
					
						if($e->getMessage() == Lang::_('E-mail already taken', 'users'))
							throw new Exception(Lang::_('E-mail already taken', 'users'));
					
					}
					
					$this->_profile->_publicname = $this->_profile->_username;
					
					$this->_profile->create();
					
					if(VPost::send_pwd() == 'yes'){
					
						$receiver = $this->_profile->_email;
						$subject = Lang::_('Your password for %site', 'users', array('site' => WS_NAME));
						$message = Lang::_('Your password', 'users').': '.VPost::pwd()."\n";
					
						$mail = new Mail($receiver, $subject, $message);
						$mail->send();
					
					}
					
					Activity::log('created the user "'.$this->_profile->_username.'"');
					
					header('Location: '.Url::_(array('ns' => 'users')));
					exit;
				
				}catch(Exception $e){
				
					$this->_action_msg .= ActionMessages::created($e->getMessage());
				
				}
			
			}
		
		}
	
	}

?>