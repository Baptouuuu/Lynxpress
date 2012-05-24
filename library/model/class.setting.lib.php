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
		* Setting
		*
		* It represents an item of the associated database table
		* Usage:
		* <code>
		*	//load a setting with all attributes filled out
		*	$element = new Setting($id);
		*
		*	//create a new setting in database
		*	$element = new Setting();
		*	$element->_name = 'admin';
		*	$element->_type = 'role';
		*	$element->create();
		*
		*	//read an attribute of a setting and then access it
		*	$element = new Setting();
		*	$element->_id = $id;
		*	$element->read('_name');
		*	echo $element->_name;
		*
		*	//update an attribute
		*	$element = new Setting($id);
		*	$element->_name = 'foobar';
		*	$element->update('_name');
		*
		*	//delete a setting
		*	$element = new Setting($id);
		*	$element->delete();
		* </code>
		*
		* @package		Library
		* @subpackage	Model
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.1
		* @final
	*/
	
	final class Setting extends Master implements Model{
	
		private $_id = null;
		private $_name = null;
		private $_type = null;
		private $_data = null;
		private $_key = null;
		
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
			
			$this->_sql_table = 'setting';
			
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
			
				$this->read('_name');
				$this->read('_type');
				$this->read('_data');
				$this->read('_key');
			
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
				$this->_name = parent::get_from_column('_name', $this->$attr, $attr, $type);
				$this->_type = parent::get_from_column('_type', $this->$attr, $attr, $type);
				$this->_data = parent::get_from_column('_data', $this->$attr, $attr, $type);
				$this->_key = parent::get_from_column('_key', $this->$attr, $attr, $type);
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Create method to add a row in setting table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => '_name', 
										  ':type' => '_type',
										  ':data' => '_data',
										  ':key' => '_key');
			$to_create['values'] = array(':name' => $this->_name, 
										 ':type' => $this->_type,
										 ':data' => $this->_data,
										 ':key' => $this->_key);
			$to_create['types'] = array(':name' => 'str',
										':type' => 'str',
										':data' => 'str',
										':key' => 'str');
			
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
			* @param	string [$attr] Setting attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] Setting attribute
			* @param	string [$type] Setting attribute data type
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
			* @param	string [$attr] Setting attribute
			* @param	mixed [$value] Setting attribute value
		*/
		
		public function __set($attr, $value){
		
			if($attr == '_name')
				$this->$attr = stripslashes($value);
			else
				$this->$attr = $value;
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] Setting attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}

?>