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
	
	namespace Admin\Plugins\Helpers;
	use \Library\Database\Database;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allow to get plugins informations easily
		*
		* @package		Admin
		* @subpackage	Plugins\Helpers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Plugins{
	
		/**
			* Retrieves plugins manifest as an array of objects
			*
			* @static
			* @access	public
			* @return	array	Array of objects
		*/
		
		public static function get_manifests(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('_data');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'plugin';
				$to_read['value_types'][':t'] = 'str';
				
				$db = new Database();
				
				$plugins = $db->read($to_read);
				
				foreach($plugins as &$p)
					$p = json_decode($p['_data']);
				
				return $plugins;
			
			}catch(Exception $e){
			
				return array();
			
			}
		
		}
	
	}

?>