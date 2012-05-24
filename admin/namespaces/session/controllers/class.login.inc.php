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
	
	namespace Admin\Session\Controllers;
	use \Admin\Master\Interfaces\Controller;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Session\Session;
	use Exception;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Lang\Lang;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Login controller
		*
		* @package		Admin
		* @subpackage	Session\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Login implements Controller{
	
		private $_session = null;
		private $_action_msg = null;
		private $_display_html = null;
		private $_header = null;
		private $_footer = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			$this->_display_html = true;
			$this->_header = 'namespaces/html/login_header.php';
			$this->_footer = 'namespaces/html/login_footer.php';
			
			if(VGet::loggedout())
				$this->_action_msg = ActionMessages::custom_good(Lang::_('You\'ve been logged out', 'session'));
			
			try{
			
				$this->_session = new Session();
				
				if(VPost::login(false))
					$this->_session->login();
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Every data are displayed via header and footer php files
			*
			* @access	public
		*/
		
		public function display_content(){
		
			//all is displayed via login.php
		
		}
		
		/**
			* Function to get attributes from outside the object
			*
			* @access	public
			* @param	string [$attr]
			* @return	mixed
		*/
		
		public function __get($attr){
		
			if(in_array($attr, array('_action_msg', '_display_html', '_header', '_footer')))
				return $this->$attr;
			else
				return 'The Lynx is not here!';
		
		}
	
	}

?>