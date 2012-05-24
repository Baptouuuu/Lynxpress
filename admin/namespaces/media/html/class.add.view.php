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
	
	namespace Admin\Media\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Media\Media;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Add html class regroup views for add controller
		*
		* @package		Admin
		* @subpackage	Media\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Add extends Master{
	
		/**
			* Display the menu of media add page
			*
			* @static
			* @access	public
			* @param	boolean [$album]
		*/
		
		public static function menu($album = false){
		
			echo '<div id="menu">'.
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'media', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media')).'">'.Lang::_('Media').'</a></span>'.
				 	(($album)?'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media', 'ctl' => 'albums')).'">'.Lang::_('Album').'</a></span>':'').
				 '</div>';
		
		}
		
		/**
			* Display submenu links
			*
			* @static
			* @access	public
			* @param	string [$type] Currently form type viewed
			* @param	boolean [$album] If user can manage albums
		*/
		
		public static function m_submenu($type, $album){
		
			parent::submenu('o');
			
			echo '<span><a '.(($type == 'upload')?'class="selected"':'').' href="'.Url::_(array('ns' => 'media', 'ctl' => 'add'), array('type' => 'upload')).'">'.Lang::_('Upload', 'media').'</a></span>'.
				 '<span><a '.(($type == 'album')?'class="selected"':'').' href="'.Url::_(array('ns' => 'media', 'ctl' => 'add'), array('type' => 'album')).'">'.Lang::_('Album').'</a></span>'.
				 '<span><a '.(($type == 'linkage')?'class="selected"':'').' href="'.Url::_(array('ns' => 'media', 'ctl' => 'add'), array('type' => 'linkage')).'">'.Lang::_('Linkage').'</a></span>'.
				 '<span><a '.(($type == 'video')?'class="selected"':'').' href="'.Url::_(array('ns' => 'media', 'ctl' => 'add'), array('type' => 'video')).'">'.Lang::_('Video').'</a></span>';
			
			parent::submenu('c');
		
		}
		
		/**
			* Display a form to upload a media
			*
			* @static
			* @access	public
		*/
		
		public static function upload(){
		
			echo '<section id="upload_form" data-url="'.Url::_(array('ns' => 'media', 'ctl' => 'ajaxadd'), array('type' => 'upload')).'">'.
					 '<section id="dropzone">Drop your files here</section>'.
					 '<label for="file">'.Lang::_('Select files to upload', 'media').':</label>&nbsp;&nbsp;&nbsp;&nbsp;<input id="file" name="file[]" type="file" multiple required />'.
					 '<input id="upload" class="button button_publish" type="submit" name="upload" value="'.Lang::_('Upload', 'media').'" /><br/>'.
					 '<span class="indication">('.Lang::_('The maximum upload file size is set to').' '.Media::max_upload().'MB)</span><br/>'.
					 '<span class="indication">('.Lang::_('If you want to upload a video too large, upload it via ftp and use this %form to register it', 'media', array('form' => '<a href="'.Url::_(array('ns' => 'media', 'ctl' => 'add'), array('type' => 'video')).'">'.Lang::_('form').'</a>')).'</span>'.
					 '<ul id="files_list">'.
					 '</ul>'.
				 '</section>';
		
		}
		
		/**
			* Display album creation form
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function album($part){
		
			if($part == 'o'){
			
				echo '<section id="upload_form">'.
						 '<input id="media_name" class="input" type="text" name="name" placeholder="'.Lang::_('Title').'" required /><br/>'.
						 '<textarea id="media_desc" class="txta" name="description" placeholder="'.Lang::_('A description of your album', 'media').'"></textarea><br/>'.
						 '<fieldset id="cats">'.
						 	'<legend>'.Lang::_('Categories').'</legend>';
			
			}elseif($part == 'c'){
			
				echo 	'</fieldset>'.
						 '<span id="comment"><input id="allow_comment" type="checkbox" name="allow_comment" value="open" /> <label for="allow_comment">'.Lang::_('Allow Comments').'</label></span>'.
						 '<span id="cover_line"><label for="cover">'.Lang::_('Cover', 'media').':</label> <input id="cover" name="cover" type="file" required /></span><br/>'.
						 '<input class="submit button button_publish" type="submit" name="create_album" value="'.Lang::_('Create').'" />'.
					 '</ssection>';
			
			}
		
		}
		
		/**
			* Display a category element
			*
			* @static
			* @access	public
			* @param	int [$id]
			* @param	string [$name]
		*/
		
		public static function category($id, $name){
		
			echo '<span class="acat"><input id="cat_'.$id.'" type="checkbox" name="category[]" value="'.$id.'" /><label for="cat_'.$id.'">'.$name.'</label></span>';
		
		}
		
		/**
			* Display video linkage form
			*
			* @static
			* @access	public
		*/
		
		public static function linkage(){
		
			echo '<section id="upload_form">'.
					 '<input id="media_name" class="input" type="text" name="name" placeholder="'.Lang::_('Title').'" required /><br/>'.
					 '<textarea id="media_desc" class="txta" name="embed_code" placeholder="'.Lang::_('Embed code', 'media').'" required></textarea>'.
					 '<input class="submit button button_publish" type="submit" name="link_alien" value="'.Lang::_('Link').'" />'.
				 '</section>';
		
		}
		
		/**
			* Display video registration form
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function video($part){
		
			if($part == 'o'){
			
				echo '<section id="upload_form">'.
						 '<input id="media_name" class="input" type="text" name="name" placeholder="'.Lang::_('Title').'" required /><br/>'.
						 '<fieldset id="cats">'.
						 	'<legend>'.Lang::_('Mime type', 'media').'</legend>';
			
			}elseif($part == 'c'){
			
				echo 	'</fieldset>'.
						 '<label for="video_url">'.Lang::_('Video url', 'media').':</label> <input id="video_url" class="input" type="text" name="url" placeholder="content/'.date('Y/m/').'" required /><br/>'.
						 '<span class="indication">('.Lang::_('Upload your video inside content folder to work correctly', 'media').'. '.Lang::_('You should use directory convention too, putting your content inside folder with year and month', 'media').'.)</span><br/>'.
						 '<input class="submit button button_publish" type="submit" name="register_video" value="'.Lang::_('Register').'" />'.
					 '</section>';
			
			}
		
		}
		
		/**
			* Display a mime element
			*
			* @static
			* @access	public
			* @param	string [$name]
		*/
		
		public static function mime($name){
		
			echo '<span class="acat"><input id="mime_'.$name.'" type="radio" name="mime" value="'.$name.'" /><label for="mime_'.$name.'">'.$name.'</label></span>';
		
		}
	
	}

?>