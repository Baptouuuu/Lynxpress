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
	use Exception;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Session
		*
		* It represents an item of the associated database table
		* Usage:
		* <code>
		*	//load a session with all attributes filled out
		*	$session = new Session($id);
		*
		*	//create a new session in database
		*	$session = new Session();
		*	$session->_user = 1;
		*	$session->_token = 'abcdef';
		*	$session->create();
		*
		*	//read an attribute of a session and then access it
		*	$session = new Session();
		*	$session->_id = $id;
		*	$session->read('_token');
		*	echo $session->_name;
		*
		*	//update an attribute
		*	$session = new Session($id);
		*	$session->_date = '2012-04-28 17:00:00';
		*	$category->update('_date');
		*
		*	//delete a session
		*	$session = new Session($id);
		*	$session->delete();
		* </code>
		*
		* @package		Library
		* @subpackage	Model
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.1
		* @final
	*/
	
	final class Session extends Master implements Model{
	
		private $_id = null;
		private $_user = null;
		private $_token = null;
		private $_date = null;
		private $_ip = null;
		
		/**
			* Cass constructor
			*
			* @access	public
			* @param	int|string [$value] Value to search
			* @param	string [$attr] Attribute to search
			* @param	string [$type] Type of the value to search
		*/
		
		public function __construct($value = false, $attr = '_id', $type = 'str'){
		
			parent::__construct();
			
			$this->_sql_table = 'session';
			
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
			
				$this->read('_user');
				$this->read('_token');
				$this->read('_date');
				$this->read('_ip');
			
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
				$this->_user = parent::get_from_column('_user', $this->$attr, $attr, $type);
				$this->_token = parent::get_from_column('_token', $this->$attr, $attr, $type);
				$this->_date = parent::get_from_column('_date', $this->$attr, $attr, $type);
				$this->_ip = parent::get_from_column('_ip', $this->$attr, $attr, $type);
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Create method to add a row in session table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':u' => '_user', 
										  ':t' => '_token',
										  ':i' => '_ip');
			$to_create['values'] = array(':u' => $this->_user, 
										 ':t' => $this->_token,
										 ':i' => $this->_ip);
			$to_create['types'] = array(':u' => 'str',
										':t' => 'str',
										':i' => 'str');
			
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
			* @param	string [$attr] Session attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] Session attribute
			* @param	string [$type] Session attribute data type
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
			* Set method to update an attribute value in the object
			*
			* @access	public
			* @param	string [$attr] Session attribute
			* @param	mixed [$value] Session attribute value
		*/
		
		public function __set($attr, $value){
		
			$this->$attr = $value;
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] Session attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}

?>