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
	use \Admin\ActionMessages\ActionMessages;
	use Exception;
	use \Library\Model\Media;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Files as VFiles;
	use \Admin\Media\Html\EditAlbum as Html;
	use \Library\Lang\Lang;
	use \Admin\Categories\Helpers\Categories;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Database\Database;
	use \Library\Model\User;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Media\Media as HMedia;
	use \Admin\Master\Helpers\Text;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* EditAlbum allows to manage an album
		*
		* @package		Admin
		* @subpackage	Media\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class EditAlbum extends Master implements Controller{
	
		private $_album = null;
		private $_pictures = null;
		private $_view = null;
		private $_categories = null;
		private $_picture = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Editing Album', 'media');
			
			if($this->_user->_permissions->album){
				
				if(VGet::view() && in_array(VGet::view(), array('album', 'picture', 'upload')))
					$this->_view = VGet::view();
				else
					$this->_view = 'album';
			
				$this->get_album();
				
				$this->create();
				$this->update();
				$this->delete();
				
				if($this->_view == 'album'){
				
					Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.labels.js');
					Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.button_confirm.js');
					Helper::add_header_link('js', WS_URL.'js/admin/core/app.localStorage.js');
					Helper::add_header_link('js', WS_URL.'js/admin/core/model.media.js');
					Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.media.editalbum.js');
				
					$this->get_pictures();
					$this->get_categories();
				
				}elseif($this->_view == 'picture'){
				
					Helper::add_header_link('js', WS_URL.'js/admin/core/app.localStorage.js');
					Helper::add_header_link('js', WS_URL.'js/admin/core/model.media.js');
					Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.media.edit.js');
					
					$this->get_picture();
				
				}elseif($this->_view == 'upload'){
				
					Helper::add_header_link('js', WS_URL.'js/admin/core/app.server.js');
					Helper::add_header_link('js', WS_URL.'js/admin/core/view.media.add.js');
					Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.media.add.js');
				
				}
			
			}
		
		}
		
		/**
			* Retrieve whished album
			*
			* @access	private
		*/
		
		private function get_album(){
		
			try{
			
				if(!VGet::id(false))
					throw new Exception(Lang::_('No album chosen', 'media'));
				
				$this->_album = new Media(VGet::id());
				
				Activity::log('started updating the album "'.$this->_album->_name.'"');
				
				$this->_album->_ouser = new User($this->_album->_user);
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
				
				$this->_album = new Media();
			
			}
		
		}
		
		/**
			* Retrieve all pictures of the retrieved album
			*
			* @access	private
		*/
		
		private function get_pictures(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('*');
				$to_read['condition_columns'][':a'] = '_attachment';
				$to_read['condition_select_types'][':a'] = '=';
				$to_read['condition_values'][':a'] = $this->_album->_id;
				$to_read['value_types'][':a'] = 'int';
				$to_read['condition_types'][':at'] = 'AND';
				$to_read['condition_columns'][':at'] = '_attach_type';
				$to_read['condition_select_types'][':at'] = '=';
				$to_read['condition_values'][':at'] = 'album';
				$to_read['value_types'][':at'] = 'str';
				
				$to_read['order'] = array('_name', 'ASC');
				
				$this->_pictures = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
				
				if(!empty($this->_pictures))
					foreach($this->_pictures as &$p)
						$p->_ouser = new User($p->_user);
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve albums categories
			*
			* @access	private
		*/
		
		private function get_categories(){
		
			try{
			
				$this->_categories = Categories::get_type('album');
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve wished picture to edit
			*
			* @access	private
		*/
		
		private function get_picture(){
		
			try{
			
				$this->_picture = new Media(VGet::pid());
			
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
			* Display an album with its pictures
			*
			* @access	private
		*/
		
		private function display_album(){
		
			Html::form('o', 'post', Url::_(array('ns' => 'media', 'ctl' => 'editalbum'), array('id' => $this->_album->_id)));
			
			Html::actions(
				$this->_album->_id,
				$this->_album->_status
			);
			
			Html::album(
				$this->_album->_id,
				$this->_album->_name,
				$this->_album->_ouser,
				$this->_album->_category,
				$this->_categories,
				$this->_album->_allow_comment,
				$this->_album->_permalink,
				$this->_album->_description,
				$this->_album->_date
			);
			
			Html::pics_actions($this->_album->_id);
			
			Html::pictures('o');
			
			if(!empty($this->_pictures))
				foreach($this->_pictures as $p)
					Html::picture(
						$p->_id,
						$p->_name,
						$p->_ouser,
						$p->_permalink,
						$p->_date,
						$this->_album->_id
					);
			else
				echo Lang::_('No pictures in this album yet', 'media');
			
			Html::pictures('c');
			
			Html::form('c');
		
		}
		
		/**
			* Display a form to upload new pictures
			*
			* @access	private
		*/
		
		private function display_upload(){
		
			Html::form('o', 'post', Url::_(array('ns' => 'media', 'ctl' => 'editalbum'), array('id' => $this->_album->_id)), true);
			
			Html::upload($this->_album->_id);
			
			Html::form('c');
		
		}
		
		/**
			* Display a form to edit an album picture
			*
			* @access	private
		*/
		
		private function display_picture(){
		
			Html::form(
				'o', 
				'post', 
				Url::_(
					array(
						'ns' => 'media', 
						'ctl' => 'editalbum'
					), 
					array(
						'id' => $this->_album->_id, 
						'view' => 'picture', 
						'pid' => $this->_picture->_id
					)
				)
			);
			
			Html::edit_picture(
				$this->_picture->_id,
				$this->_picture->_name,
				$this->_picture->_permalink,
				$this->_picture->_description,
				$this->_album->_id
			);
			
			Html::form('c');
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			Html::noscript();
			
			if($this->_user->_permissions->album){
			
				echo $this->_action_msg;
				
				$display = 'display_'.$this->_view;
				
				$this->$display();
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Uploads pictures if it's not done via ajax
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::upload() && VFiles::file()){
			
				try{
				
					$path = $this->_album->_permalink;
					
					$files = VFiles::file();
					
					foreach($files['name'] as $key => $name){
					
						if($files['error'][$key] != 0)
							throw new Exception(Lang::_('Error uploading your file').' "'.$files['name'][$key].'"');
						
						$file = new HMedia();
						$file->_mime = $files['type'][$key];
						$file->load($files['tmp_name'][$key]);
						$file->_name = Text::remove_accent($files['name'][$key]);
						
						$mime = $file->_mime;
						
						if(file_exists(PATH.$path.$file->_name))
							throw new Exception(Lang::_('File "%file" already exist', 'master', array('file' => $file->_name)));
						
						if(substr($mime, 0, 5) == 'video')
							throw new Exception(Lang::_('Can\'t associate a video to an album', 'media'));
						
						$file->save(PATH.$path.$file->_name);
						
						$media = new Media();
						
						$file->thumb(150, 0);
						$file->thumb(300, 0);
						$file->thumb(1000, 0);
						
						$media->_name = $file->_name;
						$media->_type = $mime;
						$media->_user = $this->_user->_id;
						$media->_allow_comment = 'closed';
						$media->_permalink = $path.$file->_name;
						$media->_status = 'draft';
						$media->_attachment = $this->_album->_id;
						$media->_attach_type = 'album';
						
						$media->create();
						
						Activity::log('has added the picture "'.$media->_name.'" to the album "'.$this->_album->_name.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::created($result);
			
			}
		
		}
		
		/**
			* Update album and pictures metadatas
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::save()){
			
				try{
				
					$this->_album->_name = VPost::name();
					$this->_album->_description = VPost::description();
					$this->_album->_allow_comment = VPost::allow_comment('closed');
					$this->_album->_category = implode(',', VPost::category(array()));
					
					$this->_album->update('_name');
					$this->_album->update('_description');
					$this->_album->update('_allow_comment');
					$this->_album->update('_category');
					
					foreach(VPost::all() as $key => $value){
					
						$pic = substr($key, 0, 3);
						
						if($pic == 'pic'){
						
							$id = substr($key, 3);
							$p = new Media();
							$p->_id = $id;
							$p->_name = $value;
							$p->update('_name');
						
						}
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}elseif(VPost::update_image()){
			
				try{
				
					$p = new Media(VGet::pid());
					$p->_name = VPost::name();
					$p->_description = VPost::description();
					$p->update('_name');
					$p->update('_description');
					
					if(VPost::flip('no') != 'no'){
					
						$m = new HMedia();
						$m->_mime = $p->_type;
						
						$m->load(PATH.$p->_permalink);
						$m->flip(VPost::flip());
						
						$dir = dirname($p->_permalink).'/';
						$name = basename($p->_permalink);
						
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
						$m->_mime = $p->_type;
						
						$m->load(PATH.$p->_permalink);
						$m->rotate(VPost::rotate());
						
						$dir = dirname($p->_permalink).'/';
						$name = basename($p->_permalink);
						
						$m->load(PATH.$dir.'150-'.$name);
						$m->rotate(VPost::rotate());
						
						$m->load(PATH.$dir.'300-'.$name);
						$m->rotate(VPost::rotate());
						
						$m->load(PATH.$dir.'1000-'.$name);
						$m->rotate(VPost::rotate());
						
						unset($m);
					
					}
					
					Activity::log('updated the picture "'.$p->_name.'" of the album "'.$this->_album->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}elseif(VPost::publish()){
			
				try{
				
					$this->_album->_status = 'publish';
					$this->_album->update('_status');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
			elseif(VPost::unpublish()){
			
				try{
				
					$this->_album->_status = 'draft';
					$this->_album->update('_status');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
		
		/**
			* Delete pictures from the album
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete() && VPost::picture_id() && $this->_user->_permissions->delete){
			
				try{
				
					foreach(VPost::picture_id() as $id){
					
						$p = new Media();
						$p->_id = $id;
						$p->read('_name');
						$p->read('_permalink');
						
						HMedia::delete(PATH.$p->_permalink);
						
						$p->delete();
						
						Activity::log('deleted the picture "'.$p->_name.'" of the album "'.$this->_album->_name.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VPost::delete() && VPost::picture_id() && !$this->_user->_permissions->delete){
			
				$this->_action_msg .= ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>