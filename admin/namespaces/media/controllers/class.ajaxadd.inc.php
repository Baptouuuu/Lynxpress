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
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Files as VFiles;
	use \Library\Media\Media as HMedia;
	use \Library\Model\Media;
	use \Admin\Master\Helpers\Text;
	use \Library\Lang\Lang;
	use \Admin\Activity\Helpers\Activity;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Ajax Add controller is used to upload medias via javascript calls
		*
		* To work properly this controller needs to things to be passed as POST elements
		* First it's an element named 'upload' to say a POST request is made
		* Second is files to be passed, each file as to be in a separated
		* Example of $_POST and $_FILES structures:
		* <code>
		* 	$_POST = array('upload' => 'Upload or anything else')
		*	$_FILES = array(
		*		'file0' => array(
		*			'name' => 'lynxpress.png',
		*			'tmp_name' => 'temp/whatever',
		*			'error' => 0,
		*			'size' => 10000
		*		),
		*		'file1' => array(
		*			'name' => 'lynxpress1.png',
		*			'tmp_name' => 'temp/whatever1',
		*			'error' => 0,
		*			'size' => 10000
		*		)
		*	)
		* </code>
		*
		* @package		Admin
		* @subpackage	Media\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Ajaxadd extends Master implements Controller{
	
		private $_response = null;
		private $_infos = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_display_html = false;
			
			if($this->_user->_permissions->media)
				$this->create();
		
		}
		
		/**
			* Display controller response encoded in json for an easy handling in javascript
			*
			* @access	public
		*/
		
		public function display_content(){
		
			echo json_encode(array('message' => $this->_response, 'infos' => $this->_infos));
		
		}
		
		/**
			* Upload files and save metadata in database
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::upload() && VFiles::all()){
			
				try{
				
					if(VGet::album() && $this->_user->_permissions->album){
					
						$album = new Media(VGet::album());
						$path = $album->_permalink;
					
					}else{
					
						$path = 'content/'.date('Y/m/');
					
					}
					
					foreach(VFiles::all() as $key => $file){
					
						$img = new HMedia();
						$img->load_upload($key);
						
						$name = Text::remove_accent($img->_name);
						$mime = $img->_mime;
						
						if(file_exists(PATH.$path.$name))
							throw new Exception(Lang::_('File "%file" already exist', 'master', array('file' => $name)));
						
						if(substr($mime, 0, 5) == 'video' && VGet::album())
							throw new Exception(Lang::_('Can\'t associate a video to an album', 'media'));
						
						$img->save(PATH.$path.$name);
						
						$media = new Media();
						
						if(substr($mime, 0, 5) == 'image'){
						
							$img->thumb(150, 0);
							$img->thumb(300, 0);
							$img->thumb(1000, 0);
							
							$media->_status = 'draft';
							
							if(VGet::album() && $this->_user->_permissions->album){
							
								$media->_attachment = $album->_id;
								$media->_attach_type = 'album';
							
							}else{
							
								$media->_attach_type = 'none';
							
							}
						
						}elseif(substr($mime, 0, 5) == 'video'){
						
							$media->_status = 'publish';
							$media->_attach_type = 'none';
						
						}
						
						$media->_name = $img->_name;
						$media->_type = $mime;
						$media->_user = $this->_user->_id;
						$media->_allow_comment = 'closed';
						$media->_permalink = $path.$name;
						
						$media->create();
						
						Activity::log('has uploaded a file named: '.$media->_name);
						
						$this->_response = true;
						
						$this->_infos[] = array(
							'id' => $media->_id,
							'name' => $media->_name,
							'type' => $media->_type,
							'path' => (((substr($media->_type, 0, 5) == 'image'))?WS_URL.$media->_permalink:WS_URL.'images/thumb_video.png'),
							'thumb150' => ((substr($media->_type, 0, 5) == 'image')?WS_URL.$path.'150-'.$name:''),
							'thumb300' => ((substr($media->_type, 0, 5) == 'image')?WS_URL.$path.'300-'.$name:''),
							'thumb1000' => ((substr($media->_type, 0, 5) == 'image')?WS_URL.$path.'1000-'.$name:''),
							'edit_url' => ((VGet::album() && $this->_user->_permissions->album)?Url::_(array('ns' => 'media', 'ctl' => 'editalbum'), array('id' => VGet::album(), 'view' => 'picture', 'pid' => $media->_id)):Url::_(array('ns' => 'media', 'ctl' => 'edit'), array('id' => $media->_id))),
							'edit_word' => Lang::_('Edit')
						);
					
					}
				
				}catch(Exception $e){
				
					$this->_response = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
	
	}

?>