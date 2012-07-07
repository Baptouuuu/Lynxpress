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
	
	namespace Admin\Social\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang;
	use \Admin\Social\Html\Manage as Html;
	use \Admin\ActionMessages\ActionMessages;
	use Exception;
	use \Library\Model\Setting;
	use \Library\Variable\Post as VPost;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows user to choose social networks to share to
		*
		* @package		Admin
		* @subpackage	Social\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master implements Controller{
	
		private $_setting = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Social Buttons');
			
			if($this->_user->_permissions->setting){
			
				$this->get_setting();
				
				$this->update();
			
			}
		
		}
		
		/**
			* Retrieve social buttons setting
			*
			* @access	private
		*/
		
		private function get_setting(){
		
			try{
			
				$this->_setting = new Setting('social_buttons', '_key');
				
				$this->_setting->_data = json_decode($this->_setting->_data, true);
			
			}catch(Exception $e){
			
				$this->_setting = new Setting();
				$this->_setting->_name = 'Social Buttons';
				$this->_setting->_type = 'social_buttons';
				$this->_setting->_data = json_encode(array());
				$this->_setting->_key = 'social_buttons';
				$this->_setting->create();
				
				$this->_setting->_data = json_decode($this->_setting->_data);
			
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
			* Display form to select buttons
			*
			* @access	private
		*/
		
		private function display_form(){
		
			Html::social_form($this->_setting->_data);
		
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
				
				Html::form('o', 'post', Url::_(array('ns' => 'social')));
				
				$this->display_form();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update social buttons setting
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update()){
			
				try{
				
					$this->_setting->_data = json_encode(VPost::networks(array()));
					$this->_setting->update('_data');
					
					$this->_setting->_data = json_decode($this->_setting->_data, true);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>