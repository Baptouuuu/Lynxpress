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
	
	namespace Admin\Plugins\Controllers;
	use \Admin\Master\Controllers\Controller;
	use \Library\Lang\Lang;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Plugins\Html\Add as Html;
	use \Library\Url\Url;
	use \Library\Variable\Post as VPost;
	use Exception;
	use \Admin\Plugins\Helpers\Install;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows a user to upload a plugin to the website
		*
		* @package		Admin
		* @subpackage	Plugins\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Add extends Controller{
	
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Add Plugin', 'plugins');
			
			if($this->_user->_permissions->setting){
			
				$this->create();
			
			}
		
		}
		
		/**
			* Display page menu
			*
			* @access private
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
				
				Html::form('o', 'post', Url::_(array('ns' => 'plugins', 'ctl' => 'add')), true);
				
				Html::upload();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Upload a plugin to the website
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::create()){
			
				try{
				
					$install = new Install('post', 'plugin');
					
					$error = $install->_error;
					
					if(!empty($error))
						throw new Exception($error);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::created($result);
			
			}
		
		}
	
	}

?>