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
	
	namespace Site\Contact\Controllers;
	use \Site\Master\Controllers\Controller;
	use \Library\Variable\Post as VPost;
	use \Library\Mail\Mail;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Controller used to post via AJAX email via contact form
		* HTTP POST array as to be as follow:
		* <code>
		* 	array(
		*		'email' => 'user email',
		*		'object' => 'email object',
		*		'content' => 'email content',
		*		'create' => true
		*	)
		* </code>
		*
		* @package		Site
		* @subpackage	Contact\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Ajax extends Controller{
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_display_html = false;
			
			if(VPost::create())
				$this->create();
			else
				$this->_action_msg = 'Invalid request';
		
		}
		
		/**
			* Display nothing because this controller return JSON
			*
			* @access	public
		*/
		
		public function display_menu(){}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			if(!empty($this->_action_msg))
				echo json_encode(array('message' => $this->_action_msg));
			else
				echo '{"message":true}';
		
		}
		
		/**
			* Display nothing because this controller return JSON
			*
			* @access	public
		*/
		
		public function display_sidebar(){}
		
		/**
			* Send a mail to the website administrator
			*
			* @access	private
		*/
		
		private function create(){
		
			try{
			
				if(!VPost::email(false))
					throw new Exception('email missing');
				
				if(!VPost::object(false))
					throw new Exception('object missing');
				
				if(!VPost::content(false))
					throw new Exception('content missing');
				
				$mail = new Mail(WS_EMAIL, VPost::object(), VPost::content(), VPost::email());
				$mail->send();
			
			}catch(Exception $e){
			
				$this->_action_msg = $e->getMessage();
			
			}
		
		}
		
		/**
			* Result page of the action don't have to be cached
			* Otherwise it may result on problems for the user
			*
			* @static
			* @access	public
			* @return	boolean
		*/
		
		public static function cacheable(){
		
			return false;
		
		}
	
	}

?>