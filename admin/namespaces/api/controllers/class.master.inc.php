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
	
	namespace Admin\Api\Controllers;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Database\Database;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Master class for api controllers
		*
		* @package		Admin
		* @subpackage	Api\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Master implements Controller{
	
		protected $_db = null;
		protected $_display_html = null;
		protected $_cache = null;
		protected $_action_msg = null;
		const LIFETIME = 60;				//cache lifetime set to one minute
		const FOLDER = 'cache/';			//root folder for cache files
		
		/**
			* Class constructor
			*
			* @access	protected
		*/
		
		protected function __construct(){
		
			$this->_db = new Database();
			$this->_display_html = false;
		
		}
		
		/**
			* Check if a cache file already exists anf if it's nout outdated
			*
			* @access	protected
			* @return	boolean
		*/
		
		protected function check_cache(){
		
			if(file_exists($this->_cache) && filemtime($this->_cache) > (time()-self::LIFETIME))
				return true;
			else
				return false;
		
		}
		
		/**
			* Function to get attributes from outside the object
			*
			* @access	public
			* @param	string [$attr]
			* @return	mixed
		*/
		
		public function __get($attr){
		
			if(in_array($attr, array('_display_html')))
				return $this->$attr;
			else
				return 'The Lynx is not here!';
		
		}
	
	}

?>