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
	
	namespace Library\Model;
	use \Library\Model\Interfaces\Model as Model;
	use \Admin\Users\Helpers\User as Helper;
	use Exception;
	
	defined('FOOTPRINT') or die();
	
	/**
		* User
		*
		* It represents an item of the associated database table
		* Usage:
		* <code>
		*	//load a user with all attributes filled out
		*	$element = new User($id);
		*
		*	//create a new user in database
		*	$element = new User();
		*	$element->_username = 'Foobar';
		*	$element->_role = 'admin';
		*	$element->create();
		*
		*	//read an attribute of a user and then access it
		*	$element = new User();
		*	$element->_id = $id;
		*	$element->read('_username');
		*	echo $element->_username;
		*
		*	//update an attribute
		*	$element = new User($id);
		*	$element->_username = 'foobaz';
		*	$element->update('_username');
		*
		*	//delete a user
		*	$element = new User($id);
		*	$element->delete();
		* </code>
		*
		* @package		Library
		* @subpackage	Model
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.1
		* @final
	*/
	
	final class User extends Master implements Model{
	
		private $_id = null;
		private $_username = null;
		private $_nickname = null;
		private $_firstname = null;
		private $_lastname = null;
		private $_publicname = null;
		private $_password = null;
		private $_email = null;
		private $_website = null;
		private $_msn = null;
		private $_twitter = null;
		private $_facebook = null;
		private $_google = null;
		private $_bio = null;
		private $_role = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	int|string [$value] Value to search
			* @param	string [$attr] Attribute to search
			* @param	string [$type] Type of the value to search
		*/
		
		public function __construct($value = false, $attr = '_id', $type = 'str'){
		
			parent::__construct();
			
			$this->_sql_table = 'user';
			
			if($value !== false && $attr === '_id'){
			
				$this->_id = $value;
				$this->load();
			
			}elseif($value !== false && $attr !== '_id'){
			
				$this->$attr = $value;
				$this->load_from_column($attr, $type);
			
			}
		
		}
		
		/**
			* Load method read a set of attributes at a time
			*
			* @access	public
		*/
		
		public function load(){
		
			try{
			
				$this->read('_username');
				$this->read('_nickname');
				$this->read('_firstname');
				$this->read('_lastname');
				$this->read('_publicname');
				$this->read('_password');
				$this->read('_email');
				$this->read('_website');
				$this->read('_msn');
				$this->read('_twitter');
				$this->read('_facebook');
				$this->read('_google');
				$this->read('_bio');
				$this->read('_role');
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve attributes from a specific column value
			*
			* @access	public
			* @param	string [$attr] Attribute to search
			* @param	string [$type] Type of the value to search
		*/
		
		public function load_from_column($attr, $type){
		
			try{
			
				$this->_id = parent::get_from_column('_id', $this->$attr, $attr, $type);
				$this->_username = parent::get_from_column('_username', $this->$attr, $attr, $type);
				$this->_nickname = parent::get_from_column('_nickname', $this->$attr, $attr, $type);
				$this->_firstname = parent::get_from_column('_firstname', $this->$attr, $attr, $type);
				$this->_lastname = parent::get_from_column('_lastname', $this->$attr, $attr, $type);
				$this->_publicname = parent::get_from_column('_publicname', $this->$attr, $attr, $type);
				$this->_password = parent::get_from_column('_password', $this->$attr, $attr, $type);
				$this->_email = parent::get_from_column('_email', $this->$attr, $attr, $type);
				$this->_website = parent::get_from_column('_website', $this->$attr, $attr, $type);
				$this->_msn = parent::get_from_column('_msn', $this->$attr, $attr, $type);
				$this->_twitter = parent::get_from_column('_twitter', $this->$attr, $attr, $type);
				$this->_facebook = parent::get_from_column('_facebook', $this->$attr, $attr, $type);
				$this->_google = parent::get_from_column('_google', $this->$attr, $attr, $type);
				$this->_bio = parent::get_from_column('_bio', $this->$attr, $attr, $type);
				$this->_role = parent::get_from_column('_role', $this->$attr, $attr, $type);
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Create method to add a row in user table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => '_username', 
										  ':nname' => '_nickname', 
										  ':fname' => '_firstname', 
										  ':lname' => '_lastname', 
										  ':pname' => '_publicname', 
										  ':pwd' => '_password', 
										  ':mail' => '_email', 
										  ':web' => '_website', 
										  ':msn' => '_msn', 
										  ':tweet' => '_twitter', 
										  ':fb' => '_facebook', 
										  ':gg' => '_google', 
										  ':bio' => '_bio', 
										  ':role' => '_role');
			$to_create['values'] = array(':name' => $this->_username, 
										 ':nname' => $this->_nickname, 
										 ':fname' => $this->_firstname, 
										 ':lname' => $this->_lastname, 
										 ':pname' => $this->_publicname,
										 ':pwd' => Helper::make_password($this->_username, $this->_password),
										 ':mail' => $this->_email,
										 ':web' => $this->_website,
										 ':msn' => $this->_msn,
										 ':tweet' => $this->_twitter,
										 ':fb' => $this->_facebook,
										 ':gg' => $this->_google,
										 ':bio' => $this->_bio,
										 ':role' => $this->_role);
			$to_create['types'] = array(':name' => 'str',
										':nname' => 'str',
										':fname' => 'str',
										':lname' => 'str',
										':pname' => 'str',
										':pwd' => 'str',
										':mail' => 'str',
										':web' => 'str',
										':msn' => 'str',
										':tweet' => 'str',
										':fb' => 'str',
										':gg' => 'str',
										':bio' => 'str',
										':role' => 'str');
			
			$is_int = $this->_db->create($to_create);
			
			if(is_int($is_int)){
			
				$this->_id = $is_int;
				$this->_result_action = true;
			
			}else{
			
				throw new Exception('There\'s a problem creating your '.__CLASS__);
			
			}
		
		}
		
		/**
			* Read an attribute via a given id
			*
			* @access	public
			* @param	string [$attr] User attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] User attribute
			* @param	string [$type] User attribute data type
		*/
		
		public function update($attr, $type = 'str'){
		
			parent::m_update($this->_id, $attr, $type);
		
		}
		
		/**
			* Delete the item in the database
			*
			* @access	public
		*/
		
		public function delete(){
		
			parent::m_delete($this->_id);
		
		}
		
		/**
			* Method to check if data passed via __set method are good for the object
			*
			* @access	private
			* @param	string [$attr] User attribute
			* @param	mixed [$value] User attribute value
			* @return	mixed true if no errors, otherwise return an error string
		*/
		
		private function check_data($attr, $value){
		
			switch($attr){
			
				case '_username':
					if(empty($value))
						$error = 'Username missing';
					elseif(strlen($value) > 20)
						$error = 'Username too long';
					break;
				
				case '_password':
					if(empty($value))
						$error = 'Empty password';
					break;
				
				case '_email':
					if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $value))
						$error = 'Invalid e-mail';
					elseif(empty($value))
						$error = 'E-mail missing';
					break;
				
				case '_website':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Website url has to begin with "http://"';
					break;
				
				case '_msn':
					if(!empty($value) && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $value))
						$error = 'Invalid msn address';
					break;
				
				case '_twitter':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Twitter url has to begin with "http://"';
					break;
				
				case '_facebook':
					if(!empty($value) && substr($value, 0, 7) != 'http://')
						$error = 'Facebook url has to begin with "http://"';
					break;
				
				case '_google':
					if(!empty($value) && substr($value, 0, 7) != 'http://' && substr($value, 0, 8) != 'https://')
						$error = 'Google+ url has to begin with "http://" or "https://"';
					break;
			
			}
			
			if(isset($error))
				return $error;
			else
				return true;
		
		}
		
		/**
			* Set method to update an attribute value in the object
			*
			* @access	public
			* @param	string [$attr] User attribute
			* @param	mixed [$value] User attribute value
			* @return	mixed true if no errors, otherwise return an error string
		*/
		
		public function __set($attr, $value){
		
			$checked = $this->check_data($attr, $value);
			
			if($checked === true){
			
				if(in_array($attr, array('_username', '_nickname', '_firstname', '_lastname', '_publicname', '_bio')))
					$this->$attr = stripslashes($value);
				else
					$this->$attr = $value;
				
				return true;
			
			}else{
			
				return $checked;	//contain the error message
			
			}
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] User attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}

?>