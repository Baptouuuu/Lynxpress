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
		* Media
		*
		* It represents an item of the associated database table
		* Usage:
		* <code>
		*	//load a media with all attributes filled out
		*	$element = new Media($id);
		*
		*	//create a new media in database
		*	$element = new Media();
		*	$element->_name = 'My File';
		*	$element->_type = $mime_type;
		*	$element->create();
		*
		*	//read an attribute of a media and then access it
		*	$element = new Media();
		*	$element->_id = $id;
		*	$element->read('_name');
		*	echo $element->_name;
		*
		*	//update an attribute
		*	$element = new Media($id);
		*	$element->_type = 'image/jpeg';
		*	$element->update('_type');
		*
		*	//delete a media
		*	$element = new Media($id);
		*	$element->delete();
		* </code>
		*
		* @package		Library
		* @subpackage	Model
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.1
		* @final
	*/
	
	final class Media extends Master implements Model{
	
		private $_id = null;
		private $_name = null;
		private $_type = null;
		private $_user = null;
		private $_status = null;
		private $_category = null;
		private $_allow_comment = null;
		private $_permalink = null;
		private $_embed_code = null;
		private $_description = null;
		private $_date = null;
		private $_attachment = null;
		private $_attach_type = null;
		private $_extra = null;
		
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
			
			$this->_sql_table = 'media';
			
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
				$this->read('_user');
				$this->read('_status');
				$this->read('_category');
				$this->read('_allow_comment');
				$this->read('_permalink');
				$this->read('_embed_code');
				$this->read('_description');
				$this->read('_date');
				$this->read('_attachment');
				$this->read('_attach_type');
				$this->read('_extra');
			
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
				$this->_user = parent::get_from_column('_user', $this->$attr, $attr, $type);
				$this->_status = parent::get_from_column('_status', $this->$attr, $attr, $type);
				$this->_category = parent::get_from_column('_category', $this->$attr, $attr, $type);
				$this->_allow_comment = parent::get_from_column('_allow_comment', $this->$attr, $attr, $type);
				$this->_permalink = parent::get_from_column('_permalink', $this->$attr, $attr, $type);
				$this->_embed_code = parent::get_from_column('_embed_code', $this->$attr, $attr, $type);
				$this->_description = parent::get_from_column('_description', $this->$attr, $attr, $type);
				$this->_date = parent::get_from_column('_date', $this->$attr, $attr, $type);
				$this->_attachment = parent::get_from_column('_attachment', $this->$attr, $attr, $type);
				$this->_attach_type = parent::get_from_column('_attach_type', $this->$attr, $attr, $type);
				$this->_extra = parent::get_from_column('_extra', $this->$attr, $attr, $type);
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Create method to add a row in media table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => '_name', 
										  ':type' => '_type', 
										  ':auth' => '_user', 
										  ':status' => '_status', 
										  ':cat' => '_category', 
										  ':com' => '_allow_comment', 
										  ':slug' => '_permalink', 
										  ':code' => '_embed_code', 
										  ':desc' => '_description', 
										  ':date' => '_date',
										  ':attach' => '_attachment',
										  ':attach_t' => '_attach_type',
										  ':extra' => '_extra');
			$to_create['values'] = array(':name' => $this->_name, 
										 ':type' => $this->_type, 
										 ':auth' => $this->_user, 
										 ':status' => $this->_status,
										 ':cat' => $this->_category,
										 ':com' => $this->_allow_comment,
										 ':slug' => $this->_permalink,
										 ':code' => $this->_embed_code,
										 ':desc' => $this->_description,
										 ':date' => $this->_date,
										 ':attach' => $this->_attachment,
										 ':attach_t' => $this->_attach_type,
										 ':extra' => $this->_extra);
			$to_create['types'] = array(':name' => 'str',
										':type' => 'str',
										':auth' => 'int',
										':status' => 'str',
										':cat' => 'str',
										':com' => 'str',
										':slug' => 'str',
										':code' => 'str',
										':desc' => 'str',
										':date' => 'str',
										':attach' => 'int',
										':attach_t' => 'str',
										':extra' => 'str');
			
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
			* @param	string [$attr] Media attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] Media attribute
			* @param	string [$type] Media attribute data type
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
			* @param	string [$attr] Media attribute
			* @param	mixed [$value] Media attribute value
		*/
		
		public function __set($attr, $value){
		
			if($attr == '_name')
				$this->$attr = stripslashes(trim($value));
			elseif($attr == '_description' || $attr == '_embed_code')
				$this->$attr = stripslashes($value);
			else
				$this->$attr = $value;
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] Media attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}

?>