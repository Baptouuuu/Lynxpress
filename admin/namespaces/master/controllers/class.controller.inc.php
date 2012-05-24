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
	
	namespace Admin\Master\Controllers;
	use \Library\Database\Database;
	use \Admin\Session\Session;
	use \Library\Model\User;
	use \Library\Variable\Session as VSession;
	use \Library\Model\Session as MSession;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Master
		*
		* Master class for all page controller, calls session verification methods
		*
		* Contains reusable methods which could be called every where in the admin
		*
		* @package		Admin
		* @subpackage	Master\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.1
		* @abstract
	*/
	
	abstract class Controller{
	
		protected $_db = null;
		protected $_action_msg = null;
		protected $_title = null;
		protected $_display_html = null;
		protected $_user = null;
		private $_session = null;
		protected $_header = null;
		protected $_footer = null;
		
		/**
			* Class constructor
			*
			* Constructor has to be called in each child class constructor before any function
			*
			* In order to verify session validity and then retrieve user permissions
			*
			* @access	protected
		*/
		
		protected function __construct(){
		
			if(!extension_loaded('json'))
				throw new Exception('Json not loaded');
			
			$this->_db = new Database();
			$this->_display_html = true;
			
			$this->_session = new Session();
			$this->_session->verify();
			
			$this->permission();
			
			$this->_header = 'namespaces/html/header.php';
			$this->_footer = 'namespaces/html/footer.php';
		
		}
		
		/**
			* Retrieve logged user permissions from Session class
			*
			* @access	private
		*/
		
		private function permission(){
		
			$session = new MSession(VSession::token(), '_token');
			
			$this->_user = new User($session->_user);
			
			$this->_user->_permissions = $this->_session->permission();
		
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
		
			if(in_array($attr, array('_title', '_display_html', '_header', '_footer', '_user')))
				return $this->$attr;
			else
				return 'The Lynx is not here!';
		
		}
	
	}

?>