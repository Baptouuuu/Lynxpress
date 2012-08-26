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
		* Comment
		*
		* It represents an item of the associated database table
		* Usage:
		* <code>
		*	//load a comment with all attributes filled out
		*	$comment = new Comment($id);
		*
		*	//create a new comment in database
		*	$comment = new Comment();
		*	$comment->_name = 'Some people';
		*	$comment->_rel_id = $post->_id;
		*	$comment->_rel_type = 'post';
		*	$comment->create();
		*
		*	//read an attribute of a comment and then access it
		*	$comment = new Comment();
		*	$comment->_id = $id;
		*	$comment->read('_name');
		*	echo $comment->_name;
		*
		*	//update an attribute
		*	$comment = new Category($id);
		*	$comment->_rel_type = 'album';
		*	$comment->update('_rel_type');
		*
		*	//delete a comment
		*	$comment = new Comment($id);
		*	$comment->delete();
		* </code>
		*
		* @package		Library
		* @subpackage	Model
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.1
		* @final
	*/
	
	final class Comment extends Master implements Model{
	
		private $_id = null;
		private $_name = null;
		private $_email = null;
		private $_content = null;
		private $_rel_id = null;
		private $_rel_type = null;
		private $_status = null;
		private $_date = null;
		
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
			
			$this->_sql_table = 'comment';
			
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
				$this->read('_email');
				$this->read('_content');
				$this->read('_rel_id');
				$this->read('_rel_type');
				$this->read('_status');
				$this->read('_date');
			
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
				$this->_email = parent::get_from_column('_email', $this->$attr, $attr, $type);
				$this->_content = parent::get_from_column('_content', $this->$attr, $attr, $type);
				$this->_rel_id = parent::get_from_column('_rel_id', $this->$attr, $attr, $type);
				$this->_rel_type = parent::get_from_column('_rel_type', $this->$attr, $attr, $type);
				$this->_status = parent::get_from_column('_status', $this->$attr, $attr, $type);
				$this->_date = parent::get_from_column('_date', $this->$attr, $attr, $type);
			
			}catch(Exception $e){
			
				throw new Exception(__CLASS__.' can\'t load because '.$e->getMessage());
			
			}
		
		}
		
		/**
			* Create method to add a row in comment table
			*
			* After creation success, the id of the row is inserted in id attribute
			*
			* @access	public
		*/
		
		public function create(){
		
			$to_create['table'] = $this->_sql_table;
			$to_create['columns'] = array(':name' => '_name', 
										  ':email' => '_email', 
										  ':content' => '_content', 
										  ':rid' => '_rel_id', 
										  ':rtype' => '_rel_type', 
										  ':status' => '_status');
			$to_create['values'] = array(':name' => $this->_name, 
										 ':email' => $this->_email, 
										 ':content' => $this->_content, 
										 ':rid' => $this->_rel_id,
										 ':rtype' => $this->_rel_type,
										 ':status' => $this->_status);
			$to_create['types'] = array(':name' => 'str',
										':email' => 'str',
										':content' => 'str',
										':rid' => 'int',
										':rtype' => 'str',
										':status' => 'str');
			
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
			* @param	string [$attr] Comment attribute
		*/
		
		public function read($attr){
		
			$this->$attr = parent::m_read($this->_id, $attr);
		
		}
		
		/**
			* Update the item via its id
			*
			* @access	public
			* @param	string [$attr] Comment attribute
			* @param	string [$type] Comment attribute data type
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
			* @param	string [$attr] Comment attribute
			* @param	mixed [$value] Comment attribute value
		*/
		
		public function __set($attr, $value){
		
			if($attr == '_content' || $attr == '_email')
				$this->$attr = stripslashes($value);
			elseif($attr == '_name')
				$this->$attr = stripslashes(htmlspecialchars($value));
			else
				$this->$attr = $value;
		
		}
		
		/**
			* Get method to return an object attribute value
			*
			* @access	public
			* @param	string [$attr] Comment attribute
		*/
		
		public function __get($attr){
		
			if(isset($this->$attr))
				return $this->$attr;
			else
				return false;
		
		}
	
	}

?>