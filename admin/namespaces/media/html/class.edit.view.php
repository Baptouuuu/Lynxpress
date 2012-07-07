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
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Edit class contains for edit controller
		*
		* @package		Admin
		* @subpackage	Media\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Edit extends Master{
	
		/**
			* Display the menu of media edit page
			*
			* @static
			* @access	public
			* @param	boolean [$album]
		*/
		
		public static function menu($album){
		
			echo '<div id="menu">'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media')).'">'.Lang::_('Media').'</a></span>'.
					'<span id="menu_selected" class="menu_item"><a href="#">'.Lang::_('Editing').'</a></span>'.
				 	(($album)?'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media', 'ctl' => 'albums')).'">'.Lang::_('Album').'</a></span>':'').
				 '</div>';
		
		}
		
		/**
			* Display form to edit an image
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$permalink]
			* @param	string	[$description]
		*/
		
		public static function image($id, $name, $permalink, $description){
		
			$dir = dirname($permalink).'/';
			$fname = basename($permalink);
			
			echo '<section id="edit_image" class="edit_media">'.
					'<input id="em_name" class=input type="text" name="name" value="'.$name.'" placeholder="'.Lang::_('Title').'" required x-webkit-speech />'.
					'<div id="em_media">'.
						'<a class="fancybox" href="'.WS_URL.$permalink.'">'.
							'<img src="'.WS_URL.$dir.'1000-'.$fname.'" alt="'.$fname.'" /><br/>'.
						'</a>'.
						'<div id="emm_actions">'.
							'<select name="flip">'.
								'<option value="no">'.Lang::_('Flip', 'media').'...</option>'.
								'<option value="h">'.Lang::_('Horizontally').'</option>'.
								'<option value="v">'.Lang::_('Vertically').'</option>'.
							'</select>'.
							'<select name="rotate">'.
								'<option value="no">'.Lang::_('Rotate', 'media').'...</option>'.
								'<option value="90">90°</option>'.
								'<option value="180">180°</option>'.
								'<option value="270">-90°</option>'.
							'</select>'.
						'</div>'.
					'</div>'.
					'<textarea id="em_desc" class=txta name="description" placeholder="'.Lang::_('A description of your image', 'media').'">'.$description.'</textarea><br/>'.
					'<p>'.Lang::_('Image urls', 'media').':</p>'.
					'<input class="input url" type="text" value="'.$permalink.'" readonly /> <span class="indication">('.Lang::_('full size', 'media').')</span><br/>'.
					'<input class="input url" type="text" value="'.$dir.'150-'.$fname.'" readonly /> <span class="indication">('.Lang::_('image with %width pixels width', 'media', array('width' => 150)).')</span><br/>'.
					'<input class="input url" type="text" value="'.$dir.'300-'.$fname.'" readonly /> <span class="indication">('.Lang::_('image with %width pixels width', 'media', array('width' => 300)).')</span><br/>'.
					'<input class="input url" type="text" value="'.$dir.'1000-'.$fname.'" readonly /> <span class="indication">('.Lang::_('image with %width pixels width', 'media', array('width' => 1000)).')</span><br/>'.
					'<input class="button publish" type="submit" name="update_image" value="'.Lang::_('Update').'" /> '.
					parent::clear_localstorage().
					'<input id=media_id type=hidden value="'.$id.'">'.
				 '</section>';
		
		}
		
		/**
			* Display form to edit a video
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$permalink]
			* @param	object	[$fallback] Fallback external video linked to this video
			* @param	string	[$category] Video categories
			* @param	string	[$description]
			* @param	array	[$aliens] External videos linked to the website
			* @param	array	[$cats] Video categories
		*/
		
		public static function video($id, $name, $permalink, $fallback, $category, $description, $aliens, $cats){
		
			$aid = 0;
			
			if(!empty($fallback))
				$aid = $fallback->_id;
			
			$categories = explode(',', $category);
			
			echo '<section id="edit_video" class="edit_media">'.
					'<input id="em_name" class=input type="text" name="name" value="'.$name.'" placeholder="'.Lang::_('Title').'" required x-webkit-speech />'.
					'<div id="em_media">'.
						'<video width="640" src="'.WS_URL.$permalink.'" controls preload="metadata">'.
							((!empty($fallback))?$fallback->_embed_code:'').
						'</video>'.
					'</div>'.
					'<textarea id="em_desc" class=txta name="description" placeholder="'.Lang::_('A description of your video', 'media').'">'.$description.'</textarea><br/>'.
					'<fieldset id="cats">'.
						'<legend>'.Lang::_('Categories').'</legend>';
			
						if(!empty($cats))
							foreach($cats as $c)
								self::cat($c->_id, $c->_name, ((in_array($c->_id, $categories))?true:false));
			
			echo	'</fieldset>'.
					'<select name="attach">'.
						'<option value="no">'.Lang::_('Attach to', 'media').'...</option>';
						
						if(!empty($aliens))
							foreach($aliens as $a)
								parent::option($a['_id'], $a['_name'], ($a['_id'] == $aid));
						
			echo 	'</select> '.
					'<span class="indication">'.Lang::_('Attaching a video file to an external video permits to load the last one if the user browser doesn\'t support &lt;video&gt; html5 tag', 'media').'</span><br/>'.
					'<p>'.
						Lang::_('Video url', 'media').': '.
						'<input class="input url" type="text" value="'.$permalink.'" readonly />'.
					'</p>'.
					'<input class="button publish" type="submit" name="update_video" value="'.Lang::_('Update').'" /> '.
					parent::clear_localstorage().
					'<input id=media_id type=hidden value="'.$id.'">'.
				 '</section>';
		
		}
		
		/**
			* Display a category element
			*
			* @static
			* @access	public
			* @param	mixed	[$key]
			* @param	string	[$name]
			* @param	boolean	[$selected]
		*/
		
		public static function cat($key, $name, $selected){
		
			echo '<span class="acat"><input id="cat_'.$key.'" type="checkbox" name="category[]" value="'.$key.'" '.(($selected)?'checked':'').' /><label for="cat_'.$key.'">'.$name.'</label></span>';
		
		}
		
		/**
			* Display form to edit an external video
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$description]
			* @param	string	[$embed_code]
		*/
		
		public static function alien($id, $name, $description, $embed_code){
		
			echo '<section id="edit_alien" class="edit_media">'.
					'<input id="em_name" class=input type="text" name="name" value="'.$name.'" placeholder="'.Lang::_('Title').'" required x-webkit-speech />'.
					'<div id="em_media">'.
						'<div id="emm_embed">'.
							$embed_code.
						'</div>'.
					'</div>'.
					'<textarea id="em_desc" class="txta storage" data-storage="media_description_'.$id.'" name="description" placeholder="'.Lang::_('A description of your video', 'media').'">'.$description.'</textarea><br/>'.
					'<textarea id="em_embed" class=txta name="embed_code" placeholder="'.Lang::_('Embed code', 'media').'" required>'.$embed_code.'</textarea><br/>'.
					'<input class="button publish" type="submit" name="update_alien" value="'.Lang::_('Update').'" /> '.
					parent::clear_localstorage().
					'<input id=media_id type=hidden value="'.$id.'">'.
				 '</section>';
		
		}
	
	}

?>