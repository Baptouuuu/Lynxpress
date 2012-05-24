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
	
	namespace Admin\Roles;
	use Exception;
	use \Library\Database\Database;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Roles
		*
		* Stocks user roles and their authorizations
		*
		* An authorization array is built as follows:
		* <code>
		* array(
		*	'dashboard' => boolean,
		*	'post' => boolean,
		* 	'media' => boolean,
		* 	'album' => boolean,
		* 	'comment' => boolean,
		*	'setting' => boolean,
		* 	'delete' => boolean
		* )
		*
		* //retrieve a specific role from this object
		* $roles = new Roles();
		* $my_role = $roles->_roles['admin'];
		* </code>
		* Authorization array is mainly used with _user attributes in controllers
		* in order to know if the user can manage an administration page
		*
		* @package		Admin
		* @namespace	Roles
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.1
		* @final
	*/
	
	final class Roles{
	
		private static $_roles = array();
		private static $_db = null;
		private $_corpse = array('dashboard' => false, 'post' => false, 'media' => false, 'album' => false, 'comment' => false, 'setting' => false, 'delete' => false);
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			if(empty(self::$_roles) && empty(self::$_db)){
			
				self::$_db = new Database();
				$this->get_roles();
			
			}
		
		}
		
		/**
			* Retrieve all roles stored in database
			*
			* @access	private
		*/
		
		private function get_roles(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('_id', '_name', '_data', '_key');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'role';
				$to_read['value_types'][':t'] = 'str';
				$to_read['order'] = array('_name', 'ASC');
				
				$roles = self::$_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Setting');
				
				if(!empty($roles))
					foreach($roles as $r){
					
						$r->_data = json_decode($r->_data);
						self::$_roles[$r->_name] = $r;
					
					}
			
			}catch(Exception $e){
			
				self::$_roles = array();
			
			}
		
		}
		
		/**
			* Refresh roles array attributes by retrieving again roles from database
			*
			* @access	public
		*/
		
		public function refresh(){
		
			self::$_roles = array();
			$this->get_roles();
		
		}
		
		/**
			* Function to get attributes from outside the object
			*
			* @access	public
			* @param	string [$attr]
			* @return	array
		*/
		
		public function __get($attr){
		
			if($attr == '_roles')
				return self::$_roles;
			elseif($attr == '_corpse')
				return $this->_corpse;
			else
				return 'The Lynx is not here!';
		
		}
	
	}

?>