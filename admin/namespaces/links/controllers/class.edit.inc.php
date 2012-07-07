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
	
	namespace Admin\Links\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang;
	use \Admin\Links\Html\Edit as Html;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Model\Link;
	use \Library\Variable\Get as VGet;
	use Exception;
	use \Library\Variable\Post as VPost;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allow user to edit a link
		*
		* @package		Admin
		* @subpackage	Links\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Edit extends Master implements Controller{
	
		private $_link = null;
		private $_action = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Edit Link', 'links');
			
			if($this->_user->_permissions->setting){
			
				if(VGet::action() == 'create')
					$this->_action = 'create';
				else
					$this->_action = 'update';
				
				$this->get_link();
				
				$this->update();
				$this->create();
			
			}
		
		}
		
		/**
			* Retrieve wished link
			*
			* @access	private
		*/
		
		private function get_link(){
		
			try{
			
				if($this->_action == 'create')
					$this->_link = new Link();
				else
					$this->_link = new Link(VGet::id());
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
				
				$this->_link = new Link();
			
			}
		
		}
		
		/**
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu((($this->_action == 'create')?true:false));
		
		}
		
		/**
			* Display form to edit a link
			*
			* @access	private
		*/
		
		private function display_link(){
		
			Html::link(
				$this->_link->_name,
				$this->_link->_link,
				$this->_link->_rss,
				$this->_link->_notes,
				$this->_link->_priority,
				$this->_action
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
				
				Html::form(
					'o', 
					'post', 
					Url::_(
						array('ns' => 'links', 'ctl' => 'edit'), 
							(($this->_action == 'create')?array('action' => 'create'):array('id' => VGet::id())
						)
					)
				);
				
				$this->display_link();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessgaes::part_no_perm();
			
			}
		
		}
		
		/**
			* Check if post data are valid ones
			*
			* @access	private
			* @return	boolean
		*/
		
		private function check_data(){
		
			$errors = array();
			
			if($this->_link->__set('_name', VPost::name()) !== true)
				$errors[] = $this->_link->__set('_name', VPost::name());
			else
				$this->_link->_name = VPost::name();
			
			if($this->_link->__set('_link', VPost::link()) !== true)
				$errors[] = $this->_link->__set('_link', VPost::link());
			else
				$this->_link->_link = VPost::link();
			
			if($this->_link->__set('_rss', VPost::rss()) !== true)
				$errors[] = $this->_link->__set('_rss', VPost::rss());
			else
				$this->_link->_rss = VPost::rss();
			
			if($this->_link->__set('_notes', VPost::notes()) !== true)
				$errors[] = $this->_link->__set('_notes', VPost::notes());
			else
				$this->_link->_notes = VPost::notes();
			
			if($this->_link->__set('_priority', VPost::priority()) !== true)
				$errors[] = $this->_link->__set('_priority', VPost::priority());
			else
				$this->_link->_priority = VPost::priority();
			
			if(!empty($errors)){
			
				$this->_action_msg .= ActionMessages::errors($errors);
				return false;
			
			}else{
			
				return true;
			
			}
		
		}
		
		/**
			* Create a new link
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::create() && $this->check_data()){
			
				try{
				
					$this->_link->create();
					
					Activity::log('created the link "'.$this->_link->_name.'"');
					
					header('Location: '.Url::_(array('ns' => 'links', 'ctl' => 'edit'), array('id' => $this->_link->_id)));
					exit;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::created($result);
			
			}
		
		}
		
		/**
			* Update retrieved link attributes
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update() && $this->check_data()){
			
				try{
				
					$this->_link->update('_name');
					$this->_link->update('_link');
					$this->_link->update('_rss');
					$this->_link->update('_notes');
					$this->_link->update('_priority', 'int');
					
					Activity::log('updated the link "'.$this->_link->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>