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
	use \Admin\Network\Html\Posts as Html;
	use Exception;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Model\Setting;
	use \Library\Variable\Get as VGet;
	use \Library\Lang\Lang;
	use \Library\Curl\Curl;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allow a user to see posts list for a website from his network
		*
		* @package		Admin
		* @subpackage	Network\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Posts extends Master implements Controller{
	
		private $_setting = null;
		private $_posts = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->get_setting();
			$this->get_posts();
			
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
			* Retrieve wished website posts
			*
			* @access	private
		*/
		
		private function get_posts(){
		
			try{
			
				if(!isset($this->_setting->_data->network[VGet::ws()]))
					throw new Exception(Lang::_('Website not found', 'network'));
				
				$posts = new Curl($this->_setting->_data->network[VGet::ws()]->url.'admin/?ns=api&ctl=posts&since='.substr($this->_setting->_data->last_visit, 0, 10));
				
				$this->_posts = json_decode($posts->_content);
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Build page title
			*
			* @access	private
		*/
		
		private function build_title(){
		
			if(isset($this->_setting->_data->network[VGet::ws()]))
				$this->_title = $this->_setting->_data->network[VGet::ws()]->title.' > '.Lang::_('Posts');
			else
				$this->_title = Lang::_('Posts');
		
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
			* Display the lists of posts
			*
			* @access	private
		*/
		
		private function display_posts(){
		
			Html::posts('o');
			
			if(!empty($this->_posts))
				foreach($this->_posts as $p)
					Html::post(
						$p->_id,
						$p->_title,
						$p->_content,
						$p->_date,
						$p->_user,
						$p->_tags,
						$p->_permalink,
						VGet::ws()
					);
			
			Html::posts('c');
		
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
			
			$this->display_posts();
		
		}
	
	}

?>