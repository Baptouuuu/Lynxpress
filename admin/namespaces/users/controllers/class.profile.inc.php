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
	use \Library\Lang\Lang;
	use \Admin\Users\Html\Profile as Html;
	use Exception;
	use \Library\Model\User;
	use \Library\Variable\Post as VPost;
	use \Admin\Master\Helpers\Html as Helper;
	use \Admin\Users\Helpers\User as HUser;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows user to update its profile
		*
		* @package		Admin
		* @subpackage	Users\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Profile extends Controller{
	
		private $_profile = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Profile');
			
			Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.profile.js');
			
			$this->get_profile();
			
			$this->update();
		
		}
		
		/**
			* Retrieve user profile
			*
			* @access	private
		*/
		
		private function get_profile(){
		
			try{
			
				$this->_profile = new User($this->_user->_id);
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
				
				$this->_profile = new User();
			
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
			* Display profile form
			*
			* @access	private
		*/
		
		private function display_profile(){
		
			Html::profile(
				$this->_profile->_id,
				$this->_profile->_username,
				$this->_profile->_nickname,
				$this->_profile->_firstname,
				$this->_profile->_lastname,
				$this->_profile->_publicname,
				$this->_profile->_email,
				$this->_profile->_website,
				$this->_profile->_msn,
				$this->_profile->_twitter,
				$this->_profile->_facebook,
				$this->_profile->_google,
				$this->_profile->_bio
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
			
			echo $this->_action_msg;
			
			Html::form('o', 'post', Url::_(array('ns' => 'users', 'ctl' => 'profile')));
			
			$this->display_profile();
			
			Html::form('c');
		
		}
		
		/**
			* Check if data are valid ones
			*
			* @access	private
			* @return	boolean
		*/
		
		private function check_data(){
		
			$this->_profile->_firstname = VPost::firstname();
			$this->_profile->_lastname = VPost::lastname();
			$this->_profile->_nickname = VPost::nickname();
			$this->_profile->_publicname = VPost::publicname();
			
			if($this->_profile->__set('_email', VPost::email()) !== true){
			
				$errors[] = $this->_profile->__set('_email', VPost::email());
			
			}else{
			
				try{
				
					//check if email already used
					$user = new User(VPost::email(), '_email');
					$id = $user->_id;
					
					if(!empty($id) && $id != $this->_profile->_id)
						$errors[] = Lang::_('Email already used');
					else
						$this->_profile->_email = VPost::email();
				
				}catch(Exception $e){
				
					$this->_profile->_email = VPost::email();
				
				}
			
			}
			
			if($this->_profile->__set('_website', VPost::website()) !== true)
				$errors[] = $this->_profile->__set('_website', VPost::website());
			else
				$this->_profile->_website = VPost::website();
			
			if($this->_profile->__set('_msn', VPost::msn()) !== true)
				$errors[] = $this->_profile->__set('_msn', VPost::msn());
			else
				$this->_profile->_msn = VPost::msn();
				
			if($this->_profile->__set('_twitter', VPost::twitter()) !== true)
				$errors[] = $this->_profile->__set('_twitter', VPost::twitter());
			else
				$this->_profile->_twitter = VPost::twitter();
			
			if($this->_profile->__set('_facebook', VPost::facebook()) !== true)
				$errors[] = $this->_profile->__set('_facebook', VPost::facebook());
			else
				$this->_profile->_facebook = VPost::facebook();
			
			if($this->_profile->__set('_google', VPost::google()) !== true)
				$errors[] = $this->_profile->__set('_google', VPost::google());
			else
				$this->_profile->_google = VPost::google();
			
			$this->_profile->_bio = VPost::bio();
			
			if(VPost::old_pwd() && VPost::pwd()){
			
				if($this->_user->_password !== HUser::make_password($this->_user->_username, VPost::old_pwd()))
					$errors[] = Lang::_('Old password incorect', 'users');
				elseif(VPost::pwd() !== VPost::re_pwd())
					$errors[] = Lang::_('Passwords doesn\'t match', 'users');
				elseif(VPost::pwd() == 'ninja')
					$errors[] = Lang::_('Not very stealth to choose ninja as password', 'users');
				elseif(in_array(VPost::pwd(), HUser::invalid_passwords()))
					$errors[] = Lang::_('Password two weak', 'users');
				else
					$this->_profile->_password = HUser::make_password($this->_user->_username, VPost::pwd());
			
			}
			
			if(!empty($errors)){
			
				$this->_action_msg .= ActionMessages::errors($errors);
				return false;
			
			}else{
			
				return true;
			
			}
		
		}
		
		/**
			* Update attributes of the current profile
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update() && $this->check_data()){
			
				try{
				
					$this->_profile->update('_firstname');
					$this->_profile->update('_lastname');
					$this->_profile->update('_nickname');
					$this->_profile->update('_publicname');
					$this->_profile->update('_email');
					$this->_profile->update('_website');
					$this->_profile->update('_msn');
					$this->_profile->update('_twitter');
					$this->_profile->update('_facebook');
					$this->_profile->update('_google');
					$this->_profile->update('_bio');
					
					if(VPost::old_pwd() && VPost::pwd())
						$this->_profile->update('_password');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>