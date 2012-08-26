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
	
	namespace Admin\Comments\Helpers;
	use \Library\Database\Database;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Easily manipulate multiple comments
		*
		* @package		Admin
		* @subpackage	Comments\Helpers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Comments{
	
		/**
			* Delete all comments for specifics rel_id and rel_type
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$type]
		*/
		
		public static function delete_for($id, $type){
		
			$db = new Database();
			
			$db->query('DELETE FROM '.DB_PREFIX.'comments WHERE _rel_id = '.$id.' AND _rel_type = "'.$type.'"');
		
		}
	
	}

?>