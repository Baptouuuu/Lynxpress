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
	
	namespace Template\Main;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Class identifier for the template
		* Contains methods that will be used by \Site\Templates\Helpers\Template
		* to make a bridge between this template and controllers
		*
		* @package		Template
		* @subpackage	Main
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Main{
	
		private static $_js = array();
		private static $_css = array();
		
		/**
			* Return path to the header file
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function get_header(){
		
			return 'templates/main/files/header.php';
		
		}
		
		/**
			* Return path to the footer file
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function get_footer(){
		
			return 'templates/main/files/footer.php';
		
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
		
			self::$_js[] = array('src' => $src, 'file' => $file, 'load' => $load);
		
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
		
			self::$_css[] = array('src' => $src, 'file' => $file, 'media' => $media);
		
		}
		
		/**
			* Display javascript added to the template by controller
			*
			* @static
			* @access	public
		*/
		
		public static function render_js(){
		
			foreach(self::$_js as $js)
				if($js['file'] === true)
					echo '<script type="text/javascript" src="'.$js['src'].'" '.((!empty($js['load']))?$js['load']:'').'></script>';
				else
					echo '<script type="text/javascript">'.$js['src'].'</script>';
		
		}
		
		/**
			* Display css added to the template by controller
			*
			* @static
			* @access	public
		*/
		
		public static function render_css(){
		
			foreach(self::$_css as $css)
				if($css['file'] === true)
					echo '<link rel=stylesheet type="text/css" href="'.$css['src'].'" '.((!empty($css['media']))?'media="'.$css['media'].'"':'').' />';
				else
					echo '<style>'.$css['src'].'</style>';
		
		}
		
		/**
			* Tell the template on which we are.
			* Example: array('albums', 'view', '42') means we are displaying an album with the id 42.
			* With that you can add specific css or javascript to the page you want
			*
			* @static
			* @access	public
			* @param	array	[$path] Path elements
		*/
		
		public static function publish($path){
		
			if(empty($path))
				return;
			
			if(in_array('comments', $path))
				self::add_js(WS_URL.'js/templates/main/viewModel.comment.js');
			
			if(($path[0] == 'albums' || $path[0] == 'posts') && isset($path[1]) && $path[1] == 'view'){
			
				self::add_js('(function() {var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true; po.src = \'https://apis.google.com/js/plusone.js\'; var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);})();', false);
			
			}elseif($path[0] == 'videos'){
			
				self::add_js(WS_URL.'js/templates/main/viewModel.video.js');
				self::add_js(WS_URL.'js/templates/main/viewModel.videos.js');
			
			}
		
		}
		
		/**
			* Method called every time a 404 error is reached to display custom 404 page
			*
			* @static
			* @access	public
		*/
		
		public static function display_404(){
		
			require_once 'templates/main/files/404.php';
		
		}
	
	}

?>