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
	
	namespace Site\Master\Controllers;
	use \Site\Master\Interfaces\Controller as IController;
	use \Library\Database\Database;
	use \Site\Templates\Helpers\Template;
	use \Library\Variable\Get as VGet;
	use \Site\Master\Helpers\Document;
	use \Library\Model\Setting;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Master controllers, all site controllers hace to extends this class
		* Initiate some general variables
		*
		* @package		Site
		* @subpackage	Master\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Controller implements IController{
	
		protected $_db = null;
		protected $_title = null;
		protected $_display_html = null;
		protected $_template = null;
		protected $_header = null;
		protected $_footer = null;
		protected $_menu = null;
		protected $_share = null;
		
		/**
			* Class constructor
			*
			* @access	protected
		*/
		
		protected function __construct(){
		
			$this->_db = new Database();
			$this->_display_html = true;
			
			Template::init();
			
			$this->_header = Template::get_header();
			$this->_footer = Template::get_footer();
			
			$this->_template = '\\Template\\'.Template::ns().'\\'.VGet::ns('homepage').'\\'.VGet::ctl('home');
			
			$this->_menu = Document::menu();
			
			$share = new Setting('social_buttons', '_key');
			$this->_share = json_decode($share->_data, true);
		
		}
		
		/**
			* By default any page is cacheable, if you want to prevent your page to be cached
			* you need to overload this method in your controller
			*
			* @static
			* @access	public
			* @return	boolean
		*/
		
		public static function cacheable(){
		
			return true;
		
		}
		
		/**
			* Return a message if call to undefined method
			*
			* @access	public
			* @param	string [$name] Method name
			* @param	array [$arguments] Array of all arguments passed to the unknown method
			* @return	string Error message
		*/
		
		public function __call($name, $arguments){
		
			return 'The lynx didn\'t show up calling '.$name;
		
		}
		
		/**
			* Return a message if call to undefined method in static context
			*
			* @static
			* @access	public
			* @param	string [$name] Method name
			* @param	array [$arguments] Array of all arguments passed to the unknown method
			* @return	string Error message
		*/
		
		public static function __callStatic($name, $arguments){
		
			return 'The lynx didn\'t show up calling '.$name;
		
		}
		
		/**
			* Function to get attributes from outside the object
			*
			* @access	public
			* @param	string [$attr]
			* @return	mixed
		*/
		
		public function __get($attr){
		
			if(in_array($attr, array('_title', '_display_html', '_header', '_footer')))
				return $this->$attr;
			else
				return 'The Lynx is not here!';
		
		}
	
	}

?>