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
	
	namespace Site\Master\Helpers;
	use \Library\Variable\Session as VSession;
	use \Library\Variable\Get as VGet;
	use \Library\File\File;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Handle cache for the frontend
		*
		* @package		Site
		* @subpackage	Master\Helpers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Cache{
	
		private static $_cacheable = true;
		private static $_url = null;
		const LIFETIME = 60;		//cache lifetime set to 1 minute
		
		/**
			* Init page cache handling, taking by parameter if the page has to be cached or not
			*
			* @static
			* @access	public
			* @param	boolean	[$cacheable]
		*/
		
		public static function init($cacheable){
		
			self::$_cacheable = $cacheable;
			self::$_url = 'cache/';
			
			self::build_url();
		
		}
		
		/**
			* Build the url of cache file by using 'html5' and 'renderer' session variables for the beginning of the file.
			* The rest of the file name is composed with HTTP GET parameters, by concataining attributes and values separated with dashes
			*
			* @static
			* @private
		*/
		
		private static function build_url(){
		
			if(!defined('ALLOW_CACHE') || ALLOW_CACHE === false)
				return;
			
			self::$_url .= VSession::renderer();
			
			if(VSession::html5())
				self::$_url .= '-html5';
			
			$gets = VGet::all(false);
			
			foreach($gets as $key => $value)
				self::$_url .= '-'.$key.'-'.$value;
		
		}
		
		/**
			* Return a boolean to say if a cache file exist or not.
			* If cache is deactivated it will return false so the page is generated
			*
			* @static
			* @access	public
			* @return	boolean
		*/
		
		public static function exist(){
		
			if(!defined('ALLOW_CACHE') || ALLOW_CACHE === false || self::$_cacheable === false)
				return false;
			
			$expiration = time()-self::LIFETIME;
			
			if(file_exists(self::$_url) && filemtime(self::$_url) > $expiration)
				return true;
			else
				return false;
		
		}
		
		/**
			* Build cache file in two steps, start cache session before any display and end just after displaying last html
			* and save the content to a file. If cache is disabled nothing will accur.
			*
			* @static
			* @access	public
			* @param	string	[$action] 's' or 'e'
		*/
		
		public static function build($action){
		
			if(!defined('ALLOW_CACHE') || ALLOW_CACHE === false || self::$_cacheable === false)
				return;
			
			if($action == 's'){
			
				ob_start();
			
			}elseif($action == 'e'){
			
				$content = ob_get_contents();
				
				ob_end_flush();
				
				$cache = new File();
				$cache->_content = $content;
				$cache->save(self::$_url);
			
			}
		
		}
		
		/**
			* Method called to display page from cache file
			*
			* @static
			* @access	public
		*/
		
		public static function display(){
		
			readfile(self::$_url);
		
		}
	
	}

?>