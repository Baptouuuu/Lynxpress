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
	
	namespace Admin\Media\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Admin\Media\Html\Edit as Html;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Lang\Lang;
	use Exception;
	use \Library\Media\Media as HMedia;
	use \Library\Model\Media;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Admin\Categories\Helpers\Categories;
	use \Admin\Activity\Helpers\Activity;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Edit controller allows to edit metadata of a media
		*
		* @package		Admin
		* @subpackage	Media\Controllers
		* @author		Baptiste Langlade lynxpressprg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Edit extends Master implements Controller{
	
		private $_media = null;
		private $_aliens = null;
		private $_categories = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Edit Media', 'media');
			
			if($this->_user->_permissions->media){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/localStorage.js');
				
				$this->get_media();
				
				$this->update();
			
			}
		
		}
		
		/**
			* Retriebe media from database
			*
			* @access	private
		*/
		
		private function get_media(){
		
			try{
			
				$this->_media = new Media(VGet::id());
				
				Activity::log('started to update the media "'.$this->_media->_name.'"');
				
				$id = $this->_media->_attachment;
				
				if(substr($this->_media->_type, 0, 5) == 'video' && $this->_media->_attach_type == 'fallback' && !empty($id)){
				
					$alien = new Media($this->_media->_attachment);
					
					$this->_media->_alien = $alien;
				
				}
				
				if(substr($this->_media->_type, 0, 5) == 'video'){
				
					$to_read['table'] = 'media';
					$to_read['columns'] = array('_id', '_name');
					$to_read['condition_columns'][':t'] = '_type';
					$to_read['condition_select_types'][':t'] = '=';
					$to_read['condition_values'][':t'] = 'alien';
					$to_read['value_types'][':t'] = 'str';
					
					$this->_aliens = $this->_db->read($to_read);
					
					$this->_categories = Categories::get_type('video');
				
				}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				$this->_media = new Media();
			
			}
		
		}
		
		/**
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu($this->_user->_permissions->album);
		
		}
		
		/**
			* Display form to edit an image
			*
			* @access	private
		*/
		
		private function display_image(){
		
			Html::image(
				$this->_media->_id,
				$this->_media->_name,
				$this->_media->_permalink,
				$this->_media->_description
			);
		
		}
		
		/**
			* Display form to edit a video
			*
			* @access	private
		*/
		
		private function display_video(){
		
			Html::video(
				$this->_media->_id,
				$this->_media->_name,
				$this->_media->_permalink,
				$this->_media->_alien,
				$this->_media->_category,
				$this->_media->_description,
				$this->_aliens,
				$this->_categories
			);
		
		}
		
		/**
			* Display form to edit an external video
			*
			* @access	private
		*/
		
		private function display_alien(){
		
			Html::alien(
				$this->_media->_id,
				$this->_media->_name,
				$this->_media->_description,
				$this->_media->_embed_code
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
			
			if($this->_user->_permissions->media){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'media', 'ctl' => 'edit'), array('id' => $this->_media->_id)));
				
				$display = 'display_'.substr($this->_media->_type, 0, 5);
				$this->$display();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update media metadata in database and can modify pictures
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update_image(false)){
			
				try{
				
					$this->_media->_name = VPost::name();
					$this->_media->_description = VPost::description();
					$this->_media->update('_name');
					$this->_media->update('_description');
					
					if(VPost::flip('no') != 'no'){
					
						$m = new HMedia();
						$m->_mime = $this->_media->_type;
						
						$m->load(PATH.$this->_media->_permalink);
						$m->flip(VPost::flip());
						
						$dir = dirname($this->_media->_permalink).'/';
						$name = basename($this->_media->_permalink);
						
						$m->load(PATH.$dir.'150-'.$name);
						$m->flip(VPost::flip());
						
						$m->load(PATH.$dir.'300-'.$name);
						$m->flip(VPost::flip());
						
						$m->load(PATH.$dir.'1000-'.$name);
						$m->flip(VPost::flip());
						
						unset($m);
					
					}
					
					if(VPost::rotate('no') != 'no'){
					
						$m = new HMedia();
						$m->_mime = $this->_media->_type;
						
						$m->load(PATH.$this->_media->_permalink);
						$m->rotate(VPost::rotate());
						
						$dir = dirname($this->_media->_permalink).'/';
						$name = basename($this->_media->_permalink);
						
						$m->load(PATH.$dir.'150-'.$name);
						$m->rotate(VPost::rotate());
						
						$m->load(PATH.$dir.'300-'.$name);
						$m->rotate(VPost::rotate());
						
						$m->load(PATH.$dir.'1000-'.$name);
						$m->rotate(VPost::rotate());
						
						unset($m);
					
					}
					
					Activity::log('updated the file "'.$this->_media->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}elseif(VPost::update_video(false)){
			
				try{
				
					$this->_media->_name = VPost::name();
					$this->_media->_description = VPost::description();
					$this->_media->_category = implode(',', VPost::category(array()));
					$this->_media->update('_name');
					$this->_media->update('_description');
					$this->_media->update('_category');
					
					if(VPost::attach('no') != 'no'){
					
						$this->_media->_attachment = VPost::attach();
						$this->_media->_attach_type = 'fallback';
					
					}else{
					
						$this->_media->_attachment = null;
						$this->_media->_attach_type = null;
						$this->_media->_alien = null;
					
					}
					
					$this->_media->update('_attachment', 'int');
					$this->_media->update('_attach_type');
					
					Activity::log('updated the file "'.$this->_media->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}elseif(VPost::update_alien(false)){
			
				try{
				
					$this->_media->_name = VPost::name();
					$this->_media->_description = VPost::description();
					$this->_media->_embed_code = VPost::embed_code();
					
					$this->_media->update('_name');
					$this->_media->update('_description');
					$this->_media->update('_embed_code');
					
					Activity::log('updated the file "'.$this->_media->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>