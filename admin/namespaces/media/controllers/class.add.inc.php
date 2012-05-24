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
	use Exception;
	use \Library\Lang\Lang;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Media\Html\Add as Html;
	use \Library\Variable\Get as VGet;
	use \Admin\Categories\Helpers\Categories;
	use \Library\Media\Media as HMedia;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Variable\Post as Vpost;
	use \Library\Variable\Files as VFiles;
	use \Library\Model\Media;
	use \Admin\Activity\Helpers\Activity;
	use \Admin\Master\Helpers\Text;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Add controller allow to upload medias to the website
		*
		* @package		Admin
		* @subpackage	Media\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Add extends Master implements Controller{
	
		private $_type = null;
		private $_categories = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Add media', 'media');
			
			if($this->_user->_permissions->media){
			
				if(in_array(VGet::type(), array('upload', 'album', 'linkage', 'video')))
					$this->_type = VGet::type();
				else
					$this->_type = 'upload';
				
				if($this->_type == 'upload')
					Helper::add_header_link('js', WS_URL.'js/admin/core/add.media.js');
				
				$this->get_categories();
				
				$this->create();
			
			}
		
		}
		
		/**
			* Retrieve album categories
			*
			* @access	private
		*/
		
		private function get_categories(){
		
			if($this->_type == 'album' && $this->_user->_permissions->album){
			
				$this->_categories = Categories::get_type('album');
			
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
			* Display submenu
			*
			* @access	private
		*/
		
		private function display_submenu(){
		
			Html::m_submenu($this->_type, $this->_user->_permissions->album);
		
		}
		
		/**
			* Display file upload form
			*
			* @access	private
		*/
		
		private function display_upload(){
		
			Html::upload();
		
		}
		
		/**
			* Display album creation form
			*
			* @access	private
		*/
		
		private function display_album(){
		
			Html::album('o');
			
			if(!empty($this->_categories))
				foreach($this->_categories as $c)
					Html::category($c->_id, $c->_name);
			
			Html::album('c');
		
		}
		
		/**
			* Display linkage form
			*
			* @access	private
		*/
		
		private function display_linkage(){
		
			Html::linkage();
		
		}
		
		/**
			* Display video registration form
			*
			* @access	private
		*/
		
		private function display_video(){
		
			Html::video('o');
			
			$m = new HMedia();
			
			foreach($m->_allowed as $a)
				if(substr($a, 0, 5) == 'video')
					Html::mime($a);
			
			Html::video('c');
		
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
				
				$this->display_submenu();
				
				Html::form('o', 'post', Url::_(array('ns' => 'media', 'ctl' => 'add'), array('type' => $this->_type)), true);
				
				$form = 'display_'.$this->_type;
				
				$this->$form();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Upload files and save metadata in database
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::upload(false) && VFiles::all()){
			
				try{
				
					$path = 'content/'.date('Y/m/');
					
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
						
						$file->save(PATH.$path.$file->_name);
						
						$media = new Media();
						
						if(substr($mime, 0, 5) == 'image'){
						
							$file->thumb(150, 0);
							$file->thumb(300, 0);
							$file->thumb(1000, 0);
							
							$media->_status = 'draft';
						
						}elseif(substr($mime, 0, 5) == 'video'){
						
							$media->_status = 'publish';
						
						}
						
						$media->_name = $file->_name;
						$media->_type = $mime;
						$media->_user = $this->_user->_id;
						$media->_allow_comment = 'closed';
						$media->_permalink = $path.$file->_name;
						$media->_attach_type = 'none';
						
						$media->create();
						
						Activity::log('has uploaded a file named: '.$media->_name);
					
					}
					
					header('Location: '.Url::_(array('ns' => 'media')));
					exit;
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}elseif(VPost::create_album() && $this->_user->_permissions->album){
			
				try{
				
					$path = 'content/albums/'.Text::slug(VPost::name()).'/';
					
					if(is_dir(PATH.$path))
						throw new Exception(Lang::_('Album "%album" already exist', 'media', array('album' => VPost::name())));
					
					$album = new Media();
					$album->_name = VPost::name();
					$album->_type = 'album';
					$album->_user = $this->_user->_id;
					$album->_status = 'draft';
					$album->_category = implode(',', VPost::category(array()));
					$album->_allow_comment = VPost::allow_comment('closed');
					$album->_permalink = $path;
					$album->_description = VPost::description();
					
					$cover = new HMedia();
					$cover->load_upload('cover');
					$cover->save(PATH.$path.'cover.png');
					$cover->thumb(150, 0);
					$cover->thumb(300, 0);
					$cover->thumb(1000, 0);
					
					$album->create();
					
					Activity::log('created the album "'.$album->_name.'"');
					
					header('Location: '.Url::_(array('ns' => 'media', 'ctl' => 'editalbum'), array('id' => $album->_id)));
					exit;
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}elseif(VPost::link_alien()){
			
				try{
				
					if(!VPost::name() || !VPost::embed_code())
						throw new Exception(Lang::_('Check your informations', 'actionmessages'));
					
					$alien = new Media();
					$alien->_name = VPost::name();
					$alien->_type = 'alien';
					$alien->_user = $this->_user->_id;
					$alien->_status = 'draft';
					$alien->_allow_comment = 'closed';
					$alien->_permalink = Text::slug($alien->_name);
					$alien->_embed_code = VPost::embed_code();
					$alien->_attach_type = 'none';
					
					$alien->create();
					
					Activity::log('linked the video "'.$alien->_name.'"');
					
					header('Location: '.Url::_(array('ns' => 'media'), array('type' => 'video')));
					exit;
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}elseif(VPost::register_video()){
			
				try{
				
					if(!file_exists(PATH.VPost::url()))
						throw new Exception(Lang::_('Video not found', 'media'));
					
					if(!VPost::mime())
						throw new Exception(Lang::_('Mime type missing', 'media'));
					
					$vid = new Media();
					$vid->_name = VPost::name();
					$vid->_type = VPost::mime();
					$vid->_user = $this->_user->_id;
					$vid->_status = 'publish';
					$vid->_allow_comment = 'closed';
					$vid->_permalink = VPost::url();
					$vid->_attach_type = 'none';
					
					$vid->create();
					
					Activity::log('registered a new video "'.$vid->_name.'"');
					
					header('Location: '.Url::_(array('ns' => 'media', 'ctl' => 'edit'), array('id' => $vid->_id)));
					exit;
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
	
	}

?>