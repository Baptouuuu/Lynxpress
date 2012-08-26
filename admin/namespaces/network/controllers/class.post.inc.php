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
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Lang\Lang;
	use \Library\Model\Setting;
	use \Library\Curl\Curl;
	use \Library\Variable\Get as VGet;
	use \Admin\Network\Html\Post as Html;
	use \Admin\Master\Helpers\Html as Helper;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows a user to view a post from a website of his network
		*
		* @package		Admin
		* @subpackage	Network\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Post extends Controller{
	
		private $_setting = null;
		private $_post = null;
		private $_comments = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->get_setting();
			$this->get_post();
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
			* Retrieve wished post
			*
			* @access	private
		*/
		
		private function get_post(){
		
			try{
			
				if(!isset($this->_setting->_data->network[VGet::ws()]))
					throw new Exception(Lang::_('Website not found', 'network'));
					
				$post = new Curl($this->_setting->_data->network[VGet::ws()]->url.'admin/?ns=api&ctl=posts&id='.VGet::id());
				$post = json_decode($post->_content);
				
				$this->_post = $post[0];
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve post comments if they are allowed
			*
			* @access	private
		*/
		
		private function get_comments(){
		
			if(!empty($this->_post) && $this->_post->_allow_comment == 'open'){
			
				try{
				
					$comments = new Curl($this->_setting->_data->network[VGet::ws()]->url.'admin/?ns=api&ctl=comments&type=post&id='.$this->_post->_id);
					
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
		
			if(isset($this->_setting->_data->network[VGet::ws()]) && !empty($this->_post))
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
			* Display wished post with its comments
			*
			* @access	private
		*/
		
		private function display_post(){
		
			if($this->_post->_allow_comment == 'open')
				Html::comments(
					$this->_comments,
					$this->_post->_id,
					VGet::ws()
				);
			
			Html::post(
				$this->_post->_title,
				$this->_post->_content,
				$this->_post->_date,
				$this->_post->_user,
				$this->_post->_tags,
				$this->_post->_permalink,
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
			
			$this->display_post();
		
		}
	
	}

?>