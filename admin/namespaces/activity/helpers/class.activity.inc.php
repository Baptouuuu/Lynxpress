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
	
	namespace Admin\Activity\Helpers;
	use \Library\Database\Database as Database;
	use \Library\Variable\Session as VSession;
	use \Library\Model\Session as MSession;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Activity helpers allow to manipulate activity from others controllers
		*
		* @package		Admin
		* @subpackage	Activity\Helpers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Activity{
	
		/**
			* Log an action into database, logs are viewed by administrator in the dashboard
			*
			* @static
			* @access	public
			* @param	string [$msg] Action message to log
		*/
		
		public static function log($msg){
		
			$db = new Database();
			
			$session = new MSession(VSession::token(), '_token');
			
			$to_create['table'] = 'activity';
			$to_create['columns'] = array(':id' => 'user_id', ':data' => '_data', ':date' => '_date');
			$to_create['values'] = array(':id' => $session->_user, ':data' => $msg, ':date' => date('Y-m-d H:i:s'));
			$to_create['types'] = array(':id' => 'int', ':data' => 'str', ':date' => 'str');
			
			$db->create($to_create);
		
		}
	
	}

?>