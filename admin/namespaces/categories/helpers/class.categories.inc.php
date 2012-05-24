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
	
	namespace Admin\Categories\Helpers;
	use \Library\Database\Database as Database;
	use Exception;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Categories helper
		*
		* Regroup functions to easily manipulate categories from another namespace
		*
		* @package		Admin
		* @subpackage	Categories\Helpers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Categories{
	
		/**
			* Retrieve categories of a special type
			*
			* @static
			* @access	public
			* @param	string [$type]
			* @return	array
		*/
		
		public static function get_type($type = 'post'){
		
			try{
			
				$db = new Database();
				
				$to_read['table'] = 'category';
				$to_read['columns'] = array('*');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = $type;
				$to_read['value_types'][':t'] = 'str';
				
				return $db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Category');
			
			}catch(Exception $e){
			
				return array();
			
			}
		
		}
	
	}

?>