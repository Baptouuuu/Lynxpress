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
	
	namespace Admin\Update\Controllers;
	use \Admin\Master\Controllers\Controller;
	use \Library\Lang\Lang;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Update\Html\Manage as Html;
	use \Library\Url\Url;
	use \Admin\Update\Helpers\Update;
	use \Library\Variable\Post as VPost;
	use Exception;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows a user to update its lynxpress website with the last version retrieved from lynxpress.org
		*
		* @package		Admin
		* @subpackage	Update\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Controller{
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Update');
			
			if($this->_user->_permissions->setting){
			
				$this->check();
				
				$this->update();
			
			}
		
		}
		
		/**
			* Check if there's an update available
			*
			* @access	private
		*/
		
		private function check(){
		
			try{
			
				$available = Update::check();
				
				$this->_action_msg .= ActionMessages::update_available($available);
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu();
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			Html::noscript();
			
			if($this->_user->_permissions->setting){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'update')));
				
				Html::update();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update the system
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update() && Update::check()){
			
				try{
				
					$update = new Update();
					
					$error = $update->_error;
					
					if(!empty($error))
						throw new Exception($error);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>