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
	use \Admin\Master\Controllers\Controller;
	use \Admin\Network\Html\Videos as Html;
	use Exception;
	use \Library\Model\Setting;
	use \Library\Variable\Get as VGet;
	use \Library\Lang\Lang;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Curl\Curl;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allow a user to see albums list for a website from his network
		*
		* @package		Admin
		* @subpackage	Network\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Videos extends Controller{
	
		private $_setting = null;
		private $_videos = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->get_setting();
			$this->get_videos();
			
			$this->build_title();
		
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
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve wished website videos
			*
			* @access	private
		*/
		
		private function get_videos(){
		
			try{
			
				if(!isset($this->_setting->_data->network[VGet::ws()]))
					throw new Exception(Lang::_('Website not found', 'network'));
					
				$videos = new Curl($this->_setting->_data->network[VGet::ws()]->url.'admin/?ns=api&ctl=videos&since='.substr($this->_setting->_data->last_visit, 0, 10));
				
				$this->_videos = json_decode($videos->_content);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Build page title
			*
			* @access	private
		*/
		
		private function build_title(){
		
			if(isset($this->_setting->_data->network[VGet::ws()]))
				$this->_title = $this->_setting->_data->network[VGet::ws()]->title.' > '.Lang::_('Video');
			else
				$this->_title = Lang::_('Video');
		
		}
		
		/**
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu($this->_title);
		
		}
		
		/**
			* Display videos list
			*
			* @access	private
		*/
		
		private function display_videos(){
		
			Html::videos('o');
			
			if(!empty($this->_videos))
				foreach($this->_videos as $v)
					Html::video(
						$v->_name,
						$v->_user,
						$v->_permalink,
						$v->_description,
						$v->_date,
						((isset($v->fallback))?$v->fallback:false),
						$this->_setting->_data->network[VGet::ws()]->url
					);
			
			Html::videos('c');
		
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
			
			$this->display_videos();
		
		}
	
	}

?>