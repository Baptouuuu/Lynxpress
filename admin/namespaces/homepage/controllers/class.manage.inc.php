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
	
	namespace Admin\HomePage\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang ;
	use \Admin\HomePage\Html\Manage as Html;
	use Exception;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Model\Setting;
	use \Library\Variable\Post as VPost;
	use \Library\Database\Database;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allow user to choose its front page
		*
		* @package		Admin
		* @subpackage	HomePage\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master implements Controller{
	
		private $_setting = null;
		private $_posts = null;
		private $_videos = null;
		private $_albums = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Homepage');
			
			if($this->_user->_permissions->setting){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.homepage.js');
			
				$this->get_setting();
				$this->get_posts();
				$this->get_videos();
				$this->get_albums();
				
				$this->update();
			
			}
		
		}
		
		/**
			* Retrieve homepage setting
			*
			* @access	private
		*/
		
		private function get_setting(){
		
			try{
			
				$this->_setting = new Setting('homepage', '_key');
				
				$this->_setting->_data = json_decode($this->_setting->_data);
			
			}catch(Exception $e){
			
				$this->_setting = new Setting();
				$this->_setting->_name = 'Homepage';
				$this->_setting->_type = 'homepage';
				$this->_setting->_data = json_encode(array('type' => 'post', 'view' => 'all'));
				$this->_setting->_key = 'homepage';
				$this->_setting->create();
				
				$this->_setting->_data = json_decode($this->_setting->_data);
			
			}
		
		}
		
		/**
			* Retrieve all published posts
			*
			* @access	private
		*/
		
		private function get_posts(){
		
			try{
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('_id', '_title');
				$to_read['condition_columns'][':s'] = '_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				$to_read['order'] = array('_date', 'DESC');
				
				$this->_posts = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Post');
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve videos
			*
			* @access	private
		*/
		
		private function get_videos(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('_id', '_name');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = 'LIKE';
				$to_read['condition_values'][':t'] = 'video%';
				$to_read['value_types'][':t'] = 'str';
				$to_read['order'] = array('_date', 'DESC');
				
				$this->_videos = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve published albums
			*
			* @access	private
		*/
		
		private function get_albums(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('_id', '_name');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'album';
				$to_read['value_types'][':t'] = 'str';
				$to_read['order'] = array('_date', 'DESC');
				
				$this->_albums = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
			
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
			* Display a form to choose homepage
			*
			* @access	private
		*/
		
		private function display_form(){
		
			Html::homepage(
				$this->_setting->_data,
				$this->_posts,
				$this->_videos,
				$this->_albums
			);
		
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
				
				Html::form('o', 'post', Url::_(array('ns' => 'homepage')));
				
				$this->display_form();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update homepage setting
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update()){
			
				try{
				
					$view = VPost::type();
					
					$data = array('type' => VPost::type(), 'view' => VPost::$view());
					
					$this->_setting->_data = json_encode($data);
					$this->_setting->update('_data');
					
					$this->_setting->_data = json_decode($this->_setting->_data);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>