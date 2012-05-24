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
	
	namespace Admin\Session;
	use \Library\Database\Database;
	use \Admin\Roles\Roles;
	use Exception;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Session as VSession;
	use \Library\Variable\Server as Vserver;
	use \Admin\Users\Helpers\User as Helper;
	use \Library\Model\User;
	use \Library\Model\Session as MSession;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Session
		*
		* Handles user session
		*
		* Permits to check user credentials when accessing a page
		*
		* And retrieve user permissions from the Roles class
		*
		* @package		Admin
		* @subpackage	Session
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.1
		* @final
	*/
	
	final class Session{
	
		private $_db = null;
		private $_verified = null;
		private $_roles = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			$this->_db = new Database();
			$this->_roles = new Roles();
			session_start();
		
		}
		
		/**
			* Log in the user if credentials are correct
			*
			* @access	public
		*/
		
		public function login(){
		
			$to_read['table'] = 'user';
			$to_read['columns'] = array('_id', '_username', '_password');
			$to_read['condition_columns'][':u'] = '_username';
			$to_read['condition_select_types'][':u'] = '=';
			$to_read['condition_values'][':u'] = VPost::username();
			$to_read['value_types'][':u'] = 'str';
			
			$user = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\User');
			
			if(empty($user) || $user === false){
			
				throw new Exception('Invalid username');
			
			}else{
			
				if($user[0]->_username == VPost::username() && $user[0]->_password == Helper::make_password(VPost::username(), VPost::password())){
				
					try{
					
						//if a token already exist for the user id we remove it
						$already = new MSession($user[0]->_id, '_user');
						$already->delete();
					
					}catch(Exception $e){
					
						//exception raisen if no token in database for the user or more than once
						//so in doubt we delete all existing tokens for the user
						$to_delete['table'] = 'session';
						$to_delete['condition_columns'][':u'] = '_user';
						$to_delete['condition_values'][':u'] = $user[0]->_id;
						$to_delete['value_types'][':u'] = 'int';
						
						$this->_db->delete($to_delete);
					
					}
					
					$token = time().'_'.str_shuffle(md5(time()));
					
					$session = new MSession();
					$session->_user = $user[0]->_id;
					$session->_token = $token;
					$session->_ip = $this->check_ip();
					$session->create();
					
					$_SESSION['token'] = $token;
					
					header('Location: '.Url::_(array('ns' => 'dashboard')));
					exit;
				
				}else{
				
					throw new Exception('Invalid password');
				
				}
			
			}
		
		}
		
		/**
			* Logout a user
			*
			* @access	public
		*/
		
		public function logout(){
		
			$session = new MSession(VSession::token(), '_token');
			
			Helper::update_last_visit($session->_user);
			
			$session->delete();
			
			session_destroy();
			header('Location: '.Url::_(array('ns' => 'session', 'ctl' => 'login'), array('loggedout' => 'true')));
			exit;
		
		}
		
		/**
			* Check if the session is correct, else logout the user
			*
			* @access	public
		*/
		
		public function verify(){
		
			if(VSession::token()){
				error_log(json_encode(VSession::all()));	
				try{
				
					$session = new MSession(VSession::token(), '_token');
					
					//session lifetime set to one hour
					if($session->_date < date('Y-m-d H:i:s', (time()-3600)) || $session->_ip != $this->check_ip()){
					error_log('session expired');
					error_log(json_encode(array('session_ip' => $session->_ip, 'current_ip' => $this->check_ip())));
						$session->delete();
						
						session_destroy();
						header('Location: '.Url::_(array('ns' => 'session', 'ctl' => 'login'), array('loggedout' => 'true')));
						exit;
				
					}else{
				
						$this->_verified = true;
						$session->_date = date('Y-m-d H:i:s', time());
						$session->update('_date');
				
					}
				
				}catch(Exception $e){
				error_log($e->getMessage());
					session_destroy();
					header('Location: '.Url::_(array('ns' => 'session', 'ctl' => 'login'), array('loggedout' => 'true')));
					exit;
				
				}
		
			}else{
			error_log('no token in session');
				session_destroy();
				header('Location: '.Url::_(array('ns' => 'session', 'ctl' => 'login')));
				exit;
		
			}
		
		}
		
		/**
			* If session is correct, retrieve user permissions
			*
			* @access	public
			* @return	mixed Authorization array if the user role exists, otherwise it returns false
		*/
		
		public function permission(){
		
			if($this->_verified){
			
				$session = new MSession(VSession::token(), '_token');
				
				$user = new User();
				$user->_id = $session->_user;
				$user->read('_role');
				
				if(isset($this->_roles->_roles[$user->_role]))
					return $this->_roles->_roles[$user->_role]->_data;
				
				return false;
			
			}
		
		}
		
		/**
			* Determine user ip address
			*
			* @access	private
			* @return	string
		*/
		
		private function check_ip(){
		
			if(VServer::HTTP_CLIENT_IP())
				return VServer::HTTP_CLIENT_IP();
			elseif(VServer::HTTP_X_FORWARDED_FOR())
				return VServer::HTTP_X_FORWARDED_FOR();
			else
				return VServer::REMOTE_ADDR();
		
		}
	
	}

?>