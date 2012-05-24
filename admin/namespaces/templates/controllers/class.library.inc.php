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
	
	namespace Admin\Templates\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Templates\Html\Library as Html;
	use Exception;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Request as VRequest;
	use \Library\Curl\Curl;
	use \Admin\Master\Helpers\Html as Helper;
	use \Admin\Templates\Helpers\Install;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Library controller allows user to browse official library of templates and install them directly from the list
		*
		* @package		Admin
		* @subpackage	Templates\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Library extends Master implements Controller{
	
		private $_templates = null;
		private $_page = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Library');
			
			if($this->_user->_permissions->setting){
			
				list($this->_page, $null) = Helper::pagination(0);
			
				$this->get_templates();
				
				$this->create();
			
			}
		
		}
		
		/**
			* Retrieve templates from the library on lynxpress.org
			*
			* @access	private
		*/
		
		private function get_templates(){
		
			try{
			
				$url = 'http://api.lynxpress.org/?ns=view&ctl=templates';
				
				if(VGet::search() || (VPost::search_button() && VPost::search()))
					$url .= '&search='.VRequest::search();
				
				if($this->_page > 1)
					$url .= '&p='.$this->_page;
				
				$curl = new Curl($url);
				
				$this->_templates = json_decode($curl->_content);
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
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
			* Display templates labels
			*
			* @access	private
		*/
		
		private function display_templates(){
		
			Html::actions();
			
			Html::templates('o');
			
			if(!empty($this->_templates->templates) && !isset($this->_templates->message))
				foreach($this->_templates->templates as $t)
					Html::tpl(
						$t->_id,
						$t->_name,
						$t->_description,
						$t->_downloaded
					);
			elseif(isset($this->_templates->message))
				echo ActionMessages::custom_wrong($this->_templates->message);
			else
				Html::no_tpl();
			
			Html::templates('c');
		
		}
		
		/**
			* Display pagination
			*
			* @access	private
		*/
		
		private function display_pagination(){
		
			if(isset($this->_templates->max) && $this->_templates->max > 1){
			
				$link = array();
				
				if(VRequest::search())
					$link = array('search' => VRequest::search());
				
				Html::pagination($this->_page, $this->_templates->max, $link);
			
			}
		
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
				
				Html::form('o', 'post', Url::_(array('ns' => 'templates', 'ctl' => 'library')));
				
				$this->display_templates();
				
				$this->display_pagination();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Install a new template
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VGet::action() == 'install' && VGet::id()){
			
				try{
				
					$tpl = new Install('library', VGet::id());
					
					$error = $tpl->_error;
					
					if(!empty($error))
						throw new Exception($error);
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::created($result);
			
			}
		
		}
	
	}

?>