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
	
	namespace Admin\Network\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang;
	use \Admin\Network\Html\Manage as Html;
	use Exception;
	use \Library\Model\Setting;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Curl\Curl;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows the user to view last posts of his network since the last time he logged out
		*
		* @package		Admin
		* @subpackage	Network\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master implements Controller{
	
		private $_setting = null;
		private $_sites = array();
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Network');
			
			$this->get_setting();
			$this->get_sites_infos();
		
		}
		
		/**
			* Retrieve user setting
			*
			* @access	private
		*/
		
		private function get_setting(){
		
			try{
			
				$this->_setting = new Setting('user_setting_'.$this->_user->_id, '_key');
				
				$this->_setting->_data = json_decode($this->_setting->_data);
			
			}catch(Exception $e){
			
				$this->_setting = new Setting();
				$this->_setting->_name = $this->_user->_username.' Setting';
				$this->_setting->_type = 'user_setting';
				$this->_setting->_data = json_encode(array('id' => $this->_user->_id, 'network' => array(), 'last_visit' => date('Y-m-d H:i:s')));
				$this->_setting->_key = 'user_setting_'.$this->_user->_id;
				$this->_setting->create();
				
				$this->_setting->_data = json_decode($this->_setting->_data);
			
			}
		
		}
		
		/**
			* For each sites in the user setting we retrieve number of unread items since the last user logout
			*
			* @access	private
		*/
		
		private function get_sites_infos(){
		
			try{
			
				$sites = $this->_setting->_data->network;
				
				if(!empty($sites))
					foreach($sites as $s){
					
						$posts = new Curl($s->url.'admin/?ns=api&ctl=posts&since='.substr($this->_setting->_data->last_visit, 0, 10));
						$albums = new Curl($s->url.'admin/?ns=api&ctl=albums&since='.substr($this->_setting->_data->last_visit, 0, 10));
						$videos = new Curl($s->url.'admin/?ns=api&ctl=videos&since='.substr($this->_setting->_data->last_visit, 0, 10));
						
						$s->posts = json_decode($posts->_content);
						$s->albums = json_decode($albums->_content);
						$s->videos = json_decode($videos->_content);
						
						$this->_sites[] = $s;
					
					}
			
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
			* Display the list of the websites in the network with informations about unread items since the last visit
			*
			* @access	private
		*/
		
		private function display_websites(){
		
			Html::head();
			
			Html::websites('o');
		
			if(!empty($this->_sites))
				foreach($this->_sites as $id => $s)
					Html::website(
						$id,
						$s->title,
						$s->url,
						count($s->posts),
						count($s->albums),
						count($s->videos)
					);
			else
				echo ActionMessages::custom_wrong(Lang::_('No website in your network', 'network'));
			
			Html::websites('c');
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			Html::noscript();
			
			echo $this->_action_msg;
			
			$this->display_websites();
		
		}
	
	}

?>