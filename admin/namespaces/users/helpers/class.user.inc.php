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
	
	namespace Admin\Users\Helpers;
	use \Library\Model\Setting;
	use Exception;
	use \Library\Model\User as MUser;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Helper for user
		*
		* @package		Administration
		* @subpackage	Users\Helpers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class User{
	
		/**
			* Build password hash givent to a clear password and the username associated to
			*
			* @static
			* @access	public
			* @param	string [$username]
			* @param	string [$password]
			* @return	string
		*/
		
		public static function make_password($username, $password){
		
			self::check_ext();
			
			return md5($username.SALT.$password);
		
		}
		
		/**
			* Return invalid passwords for an account
			*
			* @static
			* @access	public
			* @return	array
		*/
		
		public static function invalid_passwords(){
		
			return array('123456', 'password', '12345678', 'pussy', '12345', 'dragon', '696969', 'mustang', 'leitmen', 'baseball', 'master', 'michael', 'football', 'shadow', 'monkey', 'abc123', 'pass', 'fuckme', '6969', 'jordan', 'harley', 'ranger', 'iwantu', 'jennifer', 'hunter', 'fuck', '2000', 'test', 'batman', 'trustno1', 'thomas', 'tigger', 'robert', 'access', 'love', 'buster', '1234567', 'soccer', 'hockey', 'killer', 'george', 'sexy', 'andrew', 'charlie', 'superman', 'asshole', 'fuckyou', 'dallas', 'jessica', 'panties', 'pepper', '1111', 'austin', 'william', 'daniel', 'golfer', 'summer', 'heather', 'hammer', 'yankees', 'joshua', 'maggie', 'biteme', 'enter', 'ashley', 'thunder', 'cowboy', 'siver', 'richard', 'fucker', 'orange', 'merlin', 'michele', 'corvette', 'bigdog', 'cheese', 'matthew', '121212', 'patrick', 'martin', 'freedom', 'ginger', 'blowjob', 'nicole', 'sparky', 'yellow', 'camaro', 'secret', 'dick', 'falcon', 'taylor', '111111', '131313', '123123', 'bitch', 'hello', 'scooter', 'please', 'ninja', 'foo', 'foobar', 'foobaz', '1234', 'poiu', 'azerty', 'qwerty', '5678', '0987');
		
		}
		
		/**
			* Check if hash extension is loaded
			*
			* @static
			* @access	private
		*/
		
		private static function check_ext(){
		
			if(!extension_loaded('hash'))
				throw new Exception('Hash extension not loaded!');
		
		}
		
		/**
			* Update the last_visit property of the user setting
			*
			* @static
			* @access	public
			* @param	integer	[$id] User id
		*/
		
		public static function update_last_visit($id){
		
			try{
			
				$setting = new Setting('user_setting_'.$id, '_key');
				$setting->_data = json_decode($setting->_data);
				$setting->_data->last_visit = date('Y-m-d H:i:s');
				$setting->_data = json_encode($setting->_data);
				
				$setting->update('_data');
			
			}catch(Exception $e){
			
				$user = new MUser($id);
				
				$setting = new Setting();
				$setting->_name = $user->_username.' Setting';
				$setting->_type = 'user_setting';
				$setting->_data = json_encode(array('id' => $id, 'network' => array(), 'last_visit' => date('Y-m-d H:i:s')));
				$setting->_key = 'user_setting_'.$id;
				
				$setting->create();
			
			}
		
		}
	
	}

?>