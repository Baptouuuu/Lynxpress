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
	use Exception;
	use \Library\Model\Setting;
	use \Library\Variable\Post as VPost;
	use \Library\Curl\Curl;
	use \Library\Lang\Lang;
	use \Admin\ActionMessages\ActionMessages;
	
	/**
		* Used to send comments to a website of the user network
		* To work you have to access this controller in POST method, with an array as follows
		* <code>
		* 	array(
		*		'type' => 'post or media',
		*		'id' => 'id of the post or the media',
		*		'website' => 'id of the website in the user setting',
		*		'content' => 'message you want to send'
		*	);
		* </code>
		*
		* @package		Admin
		* @subpackage	Network\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Comments extends Controller{
	
		private $_setting = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_display_html = false;
			
			$this->get_setting();
			
			$this->create();
		
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
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			header('Content-type: application/json; charset=utf-8');
			
			echo json_encode(array('message' => $this->_action_msg));
		
		}
		
		/**
			* Send a comment on the wished website of the user network
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::type() && VPost::id() && isset($_POST['website']) && VPost::content()){
			
				try{
				
					$curl = new Curl();
					$curl->_post = true;
					$curl->_data = array(
						'name' => $this->_user->_publicname,
						'email' => $this->_user->_email,
						'content' => VPost::content(),
						'id' => VPost::id(),
						'type' => VPost::type(),
						'create' => true
					);
					$curl->_url = $this->_setting->_data->network[VPost::website()]->url.'admin/?ns=api&ctl=comments';
					
					$curl->connect();
					
					$r = json_decode($curl->_content);
					
					if($r->message !== true)
						throw new Exception($r->message);
				
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::created($result);
			
			}else{
			
				$this->_action_msg .= ActionMessages::custom_wrong(Lang::_('Invalid Request'));
			
			}
		
		}
	
	}

?>