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
	use \Admin\Network\Html\Album as Html;
	use \Library\Model\Setting;
	use Exception;
	use \Library\Variable\Get as VGet;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Curl\Curl;
	use \Admin\Master\Helpers\Html as Helper;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows a user to view wished album from his network
		*
		* @package		Admin
		* @package		Network\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.coù
		* @version		1.0
		* @final
	*/
	
	final class Album extends Master implements Controller{
	
		private $_setting = null;
		private $_album = null;
		private $_comments = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->get_setting();
			$this->get_album();
			$this->get_comments();
			
			$this->build_title();
			
			Helper::add_header_link('js', WS_URL.'js/admin/core/app.server.js');
			Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.network.comments.js');
		
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
			* Retrieve wished album from the website
			*
			* @access	private
		*/
		
		private function get_album(){
		
			try{
			
				if(!isset($this->_setting->_data->network[VGet::ws()]))
					throw new Exception(Lang::_('Website not found', 'network'));
				
				$album = new Curl($this->_setting->_data->network[VGet::ws()]->url.'admin/?ns=api&ctl=albums&id='.VGet::id());
				$album = json_decode($album->_content);
				
				$this->_album = $album[0];
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve album comments if they are allowed
			*
			* @access	private
		*/
		
		private function get_comments(){
		
			if(!empty($this->_album) && $this->_album->_allow_comment == 'open'){
			
				try{
				
					$comments = new Curl($this->_setting->_data->network[VGet::ws()]->url.'admin/?ns=api&ctl=comments&type=media&id='.VGet::id());
					$this->_comments = json_decode($comments->_content);
				
				}catch(Exception $e){
				
					$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Build page title
			*
			* @access	private
		*/
		
		private function build_title(){
		
			if(isset($this->_setting->_data->network[VGet::ws()]))
				$this->_title = $this->_setting->_data->network[VGet::ws()]->title.' > '.Lang::_('Album');
			else
				$this->_title = Lang::_('Album');
		
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
			* Display album with its comments
			*
			* @access	private
		*/
		
		private function display_album(){
		
			if($this->_album->_allow_comment == 'open')
				Html::comments(
					$this->_comments,
					$this->_album->_id,
					VGet::ws()
				);
			
			Html::album(
				$this->_album->_id,
				$this->_album->_name,
				$this->_album->_user,
				$this->_album->_permalink,
				$this->_album->_description,
				$this->_album->_date,
				$this->_setting->_data->network[VGet::ws()]->url,
				$this->_setting->_data->network[VGet::ws()]->title
			);
			
			Html::pictures(
				$this->_album->pictures,
				$this->_setting->_data->network[VGet::ws()]->url,
				$this->_setting->_data->network[VGet::ws()]->title
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
			
			echo $this->_action_msg;
			
			$this->display_album();
		
		}
	
	}

?>