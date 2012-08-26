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
	
	namespace Site\Templates\Helpers;
	use \Library\Model\Setting;
	use Exception;
	use \Library\Variable\Get as VGet;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Bridge controllers to the current template
		* Contains method to call template functions to display data
		*
		* @package		Site
		* @subpackage	Templates\Helpers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Template{
	
		private static $_namespace = null;
		private static $_main = null;
		
		/**
			* Initiate template helper by retrieving some data about the current template
			*
			* @static
			* @access	public
		*/
		
		public static function init(){
		
			try{
			
				$tpl = new Setting('current_template', '_key');
				
				self::$_namespace = $tpl->_data;
				self::$_main = '\\Template\\'.ucfirst(self::$_namespace).'\\Main';
			
			}catch(Exception $e){
			
				self::$_namespace = 'main';
				self::$_main = '\\Template\\Main\\Main';
			
			}
		
		}
		
		/**
			* Return the path to the template header
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function get_header(){
		
			$class = self::$_main;
			
			return $class::get_header();
		
		}
		
		/**
			* Return the path to the template footer
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function get_footer(){
		
			$class = self::$_main;
			
			return $class::get_footer();
		
		}
		
		/**
			* Add a javascript file to the template
			*
			* @static
			* @access	public
			* @param	string	[$src] Path to the js file or inline js
			* @param	boolean	[$file] If src is inline js this param has to be set to false
			* @param	string	[$load] Load method for the file (async or defer)
		*/
		
		public static function add_js($src, $file = true, $load = ''){
		
			$class = self::$_main;
			
			$class::add_js($src, $file, $load);
		
		}
		
		/**
			* Add a css file to the template
			*
			* @static
			* @access	public
			* @param	string	[$src] Path to the css file or inline css
			* @param	boolean	[$file] If src is inline css this param has to be set to false
			* @param	string	[$media] Media query to target a specific screen
		*/
		
		public static function add_css($src, $file = true, $media = ''){
		
			$class = self::$_main;
			
			$class::add_css($src, $file, $media);
		
		}
		
		/**
			* Tells if a method exist in a template or not
			* Useful to know if a controller can call a template method to render its data
			* or it has to be rendered by a method inside its namespace
			*
			* @static
			* @access	public
			* @param	string	[$method] Class method
			* @param	string	[$namespace] Namespace where is located the class
			* @param	string	[$class] Class name
			* @return	boolean
		*/
		
		public static function _callable($method, $namespace = false, $class = false){
		
			try{
			
				if($namespace === false)
					$namespace = VGet::ns();
				
				if($class === false)
					$class = VGet::ctl('home');
				
				if(method_exists('\\Template\\'.ucfirst(self::$_namespace).'\\'.ucfirst($namespace).'\\'.ucfirst($class), $method))
					return true;
				else
					return false;
			
			}catch(Exception $e){
			
				return false;
			
			}
		
		}
		
		/**
			* Return current template namespace
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function ns(){
		
			return self::$_namespace;
		
		}
		
		/**
			* Tell the template on which we are.
			* Example: albums.view.42 means we are displaying an album with the id 42
			*
			* @static
			* @access	public
			* @param	string	[$path]
		*/
		
		public static function publish($path){
		
			$class = self::$_main;
			
			$class::publish(explode('.', $path));
		
		}
		
		/**
			* Calls the template method display_404
			*
			* @static
			* @access	public
		*/
		
		public static function display_404(){
		
			$class = self::$_main;
			
			$class::display_404();
		
		}
	
	}

?>