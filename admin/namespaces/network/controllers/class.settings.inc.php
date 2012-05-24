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
	use \Admin\Network\Html\Settings as Html;
	use \Library\Url\Url;
	use Exception;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Model\Setting;
	use \Library\Variable\Post as VPost;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Curl\Curl;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows user to add or remove websites from his network
		*
		* @package		Admin
		* @subpackage	Network\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Settings extends Master implements Controller{
	
		private $_setting = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Settings');
			
			Helper::add_header_link('js', WS_URL.'js/admin/core/labels.js');
			
			$this->get_setting();
			
			$this->create();
			$this->delete();
		
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
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu();
		
		}
		
		/**
			* Display network websites with a form to add one
			*
			* @access	private
		*/
		
		private function display_setting(){
		
			Html::add_form();
			
			Html::actions();
			
			Html::websites('o');
			
			if(!empty($this->_setting->_data->network))
				foreach($this->_setting->_data->network as $key => $w)
					Html::website(
						$key,
						$w->title,
						$w->url
					);
			else
				Html::no_website();
			
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
			
			Html::form('o', 'post', Url::_(array('ns' => 'network', 'ctl' => 'settings')));
			
			$this->display_setting();
			
			Html::form('c');
		
		}
		
		/**
			* Add a new website to the user network
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::create()){
			
				try{
				
					if(!VPost::title() || !VPost::url())
						throw new Exception(Lang::_('Informations missing', 'network'));
					
					$url = VPost::url().'admin/?ns=api&ctl=check';
					
					$curl = new Curl($url);
					
					$rsp = json_decode($curl->_content);
					
					if($rsp == null || (!isset($rsp->message) && $rsp->message !== true))
						throw new Exception(Lang::_('The website do not use Lynxpress or not a compatible version', 'network'));
					
					$network = $this->_setting->_data->network;
					
					$network[] = array('title' => VPost::title(), 'url' => VPost::url());
					
					$this->_setting->_data->network = $network;
					$this->_setting->_data = json_encode($this->_setting->_data);
					
					$this->_setting->update('_data');
					
					$this->_setting->_data = json_decode($this->_setting->_data);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::created($result);
			
			}
		
		}
		
		/**
			* Delete website(s) from the user network
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete() && VPost::ws()){
			
				try{
				
					//tranform network object in array so we can unset correctly our arrays
					$network = json_encode($this->_setting->_data->network);
					$network = json_decode($network, true);
					
					foreach(VPost::ws() as $id)
						foreach($network as $key => $ws)
							if($key == $id)
								unset($network[$key]);
					
					$this->_setting->_data->network = $network;
					$this->_setting->_data = json_encode($this->_setting->_data);
					$this->_setting->update('_data');
					
					$this->_setting->_data = json_decode($this->_setting->_data);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::deleted($result);
			
			}
		
		}
	
	}

?>