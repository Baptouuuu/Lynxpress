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
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang;
	use \Library\Database\Database;
	use \Admin\Users\Html\Reset as Html;
	use \Library\Variable\Post as VPost;
	use \Library\Model\User;
	use \Library\Mail\Mail;
	use \Admin\Users\Helpers\User as HUser;
	use Exception;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allow a user to reset its password
		*
		* @package		Admin
		* @subpackage	Users\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Reset implements Controller{
	
		private $_db = null;
		private $_title = null;
		private $_action_msg = null;
		private $_display_html = null;
		private $_header = null;
		private $_footer = null;
		private $_sent = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			$this->_db = new Database();
			$this->_title = Lang::_('Reset Password', 'users');
			
			$this->_display_html = true;
			$this->_header = 'namespaces/html/reset_header.php';
			$this->_footer = 'namespaces/html/reset_footer.php';
			
			$this->update();
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			Html::form('o', 'post', Url::_(array('ns' => 'users', 'ctl' => 'reset')));
			
			if($this->_sent === true)
				Html::sent();
			else
				Html::reset_form($this->_action_msg);
			
			Html::form('c');
		
		}
		
		/**
			* Update password of the user
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update()){
			
				try{
				
					try{
					
						$user = new User(VPost::email(), '_email');
					
					}catch(Exception $e){
					
						throw new Exception(Lang::_('Unknown e-mail', 'users'));
					
					}
					
					$pwd = substr(str_shuffle(md5(time())), 0, 8);
					
					$user->_password = HUser::make_password($user->_username, $pwd);
					$user->update('_password');
					
					$mail = new Mail(
						$user->_email,
						Lang::_('Your password for %site', 'users', array('site' => WS_NAME)),
						Lang::_('Your password', 'users').': '.$pwd
					);
					$mail->send();
					
					$this->_sent = true;
				
				}catch(Exception $e){
				
					$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Return a message if call to undefined method
			*
			* @access	public
			* @param	string [$name] Method name
			* @param	array [$arguments] Array of all arguments passed to the unknown method
			* @return	string Error message
		*/
		
		public function __call($name, $arguments){
		
			return 'The lynx didn\'t show up calling '.$name;
		
		}
		
		/**
			* Return a message if call to undefined method in static context
			*
			* @static
			* @access	public
			* @param	string [$name] Method name
			* @param	array [$arguments] Array of all arguments passed to the unknown method
			* @return	string Error message
		*/
		
		public static function __callStatic($name, $arguments){
		
			return 'The lynx didn\'t show up calling '.$name;
		
		}
		
		/**
			* Function to get attributes from outside the object
			*
			* @access	public
			* @param	string [$attr]
			* @return	mixed
		*/
		
		public function __get($attr){
		
			if(in_array($attr, array('_title', '_display_html', '_header', '_footer', '_action_msg')))
				return $this->$attr;
			else
				return 'The Lynx is not here!';
		
		}
	
	}

?>