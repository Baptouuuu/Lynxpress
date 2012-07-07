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
		* EditAlbum html class contains views for editalbum controller
		*
		* @package		Admin
		* @subpackage	Media\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class EditAlbum extends Master{
	
		/**
			* Display the menu of media editalbum page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media')).'">'.Lang::_('Media').'</a></span>'.
				 	'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media', 'ctl' => 'albums')).'">'.Lang::_('Album').'</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="#">'.Lang::_('Editing Album', 'media').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display album actions
			*
			* @static
			* @access	public
			* @param	integer	[$id] Album id
			* @param	string	[$status] Album status
		*/
		
		public static function actions($id, $status){
		
			echo '<div id="actions">'.
				 	'<input class="button" type="submit" name="save" value="'.Lang::_('Save').'" /> '.
				 	'<a class="button" href="'.Url::_(array('ns' => 'albums', 'id' => $id), array(), true).'" target=_blank>'.Lang::_('View').'</a> '.
				 	parent::clear_localstorage().
				 	'<input class="button publish" type="submit" '.(($status == 'draft')?'name="publish" value="'.Lang::_('Publish').'"':'name="unpublish" value="'.Lang::_('Unpublish').'"').' />'.
				 '</div>';
		
		}
		
		/**
			* Display form to edit an album
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	object	[$user]
			* @param	array	[$a_cat] Album categories
			* @param	array	[$categories] Categories available for albums
			* @param	string	[$allow_comment]
			* @param	string	[$permalink]
			* @param	string	[$description]
			* @param	string	[$date]
		*/
		
		public static function album($id, $name, $user, $a_cat, $categories, $allow_comment, $permalink, $description, $date){
		
			echo '<section id="album_form">'.
				 	'<div id="af_infos">'.
				 		'<div id="afi_thumb">'.
				 			'<img src="'.WS_URL.$permalink.'300-cover.png" alt="cover" />'.
				 		'</div>'.
				 		'<div id="afi_meta">'.
				 			Lang::_('Created by').': '.$user->_username.'<br/>'.
				 			Lang::_('the').': '.date('d/m/Y @ H:i', strtotime($date)).'<br/>'.
				 			'<span id="comment">'.
				 				'<input id="allow_comment" type="checkbox" name="allow_comment" value="open" '.(($allow_comment == 'open')?'checked':'').' />'.
				 				'<label for="allow_comment">'.Lang::_('Allow Comments').'</label>'.
				 			'</span>'.
				 			'<input id=media_id type=hidden value="'.$id.'">'.
				 		'</div>'.
				 	'</div>'.
				 	'<div id="af_content">'.
				 		'<input id=ea_name class=input name="name" value="'.$name.'" placeholder="'.Lang::_('Title').'" required x-webkit-speech /><br/>'.
				 		'<textarea id=ea_desc class=txta name="description" placeholder="'.Lang::_('A description of your album', 'media').'">'.$description.'</textarea>'.
				 		'<fieldset>'.
				 			'<legend>'.Lang::_('Categories').'</legend>';
				 		
				 			$cats = explode(',', $a_cat);
				 			
				 			foreach($categories as $c)
				 				echo '<span class="acat"><input id="cat_'.$c->_id.'" type="checkbox" name="category[]" value="'.$c->_id.'" '.((in_array($c->_id, $cats))?'checked':'').'><label for="cat_'.$c->_id.'">'.$c->_name.'</label></span>';
				 		
			echo 		'</fieldset>'.
				 	'</div>'.
				 '</section>';
		
		}
		
		/**
			* Display actions available for album pictures
			*
			* @static
			* @access	public
			* @param	integer	[$id] Album id
		*/
		
		public static function pics_actions($id){
		
			echo '<div id="pics_actions">'.
					'<input class="button delete" type="submit" name="delete" value="'.Lang::_('Delete Pictures', 'media').'" data-confirm="'.Lang::_('Really').'?" /> '.
					'<a class="button" href="'.Url::_(array('ns' => 'media', 'ctl' => 'editalbum'), array('id' => $id, 'view' => 'upload')).'">'.Lang::_('Upload Pictures', 'media').'</a>'.
					' <span class="indication">('.Lang::_('Indication: pictures are ordered with their name', 'media').')</span>'.
				 '</div>';
		
		}
		
		/**
			* Display pictures labels structure list
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function pictures($part){
		
			if($part == 'o'){
			
				echo '<section id="pictures_labels" class="labels">';
			
			}elseif($part == 'c'){
			
				echo '</section>';
			
			}
		
		}
		
		/**
			* Display a picture label
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	object	[$user]
			* @param	string	[$permalink]
			* @param	string	[$date]
			* @param	integer	[$aid] Album id
		*/
		
		public static function picture($id, $name, $user, $permalink, $date, $aid){
		
			$dir = dirname($permalink).'/';
			$fname = basename($permalink);
			
			echo '<div id="label_'.$id.'" class="label">'.
				 	'<label for="pic_'.$id.'">'.
				 		'<div class="check_label">'.
				 			'<input id="pic_'.$id.'" type="checkbox" name="picture_id[]" value="'.$id.'" />'.
				 		'</div>'.
				 	'</label>'.
				 	'<div class="thumb">'.
				 		'<a href="'.Url::_(array('ns' => 'media', 'ctl' => 'editalbum'), array('id' => $aid, 'view' => 'picture', 'pid' => $id)).'">'.
				 			'<img src="'.WS_URL.$dir.'150-'.$fname.'" alt="'.$name.'" />'.
				 		'</a>'.
				 	'</div>'.
				 	'<div class="content">'.
				 		'<span class=label>'.Lang::_('Name').':</span> <input class="input" type="text" name="pic'.$id.'" value="'.$name.'" required /><br/>'.
				 		'<span class=label>'.Lang::_('User').':</span> '.$user->_username.'<br/>'.
				 		'<span class=label>'.Lang::_('Creation').':</span> '.date('d/m/Y @ H:i', strtotime($date)).''.
				 	'</div>'.
				 '</div>';
		
		}
		
		/**
			* Display an upload form to add pictures to an album
			*
			* @static
			* @access	public
			* @param	integer	[$id] Album id
		*/
		
		public static function upload($id){
		
			echo '<section id="upload_form" data-url="'.Url::_(array('ns' => 'media', 'ctl' => 'ajaxadd'), array('type' => 'upload', 'album' => $id)).'">'.
					 '<a class="button" href="'.Url::_(array('ns' => 'media', 'ctl' => 'editalbum'), array('id' => $id)).'">'.Lang::_('Go Back').'</a><br/>'.
					 '<br/>'.
					 '<section id="dropzone">'.Lang::_('Drop your files here', 'media').'</section>'.
					 '<label for="file">'.Lang::_('Select files to upload', 'media').':</label>&nbsp;&nbsp;&nbsp;&nbsp;<input id="file" name="file[]" type="file" multiple required />'.
					 '<input id="upload" class="button publish" type="submit" name="upload" value="'.Lang::_('Upload', 'media').'" /><br/>'.
					 '<span class="indication">('.Lang::_('The maximum upload file size is set to', 'media').' '.Media::max_upload().'MB)</span><br/>'.
					 '<ul id="files_list">'.
					 '</ul>'.
				 '</section>'.
				 '<script id=tpl_media type="media/template">'.
				 	'<li id=item_{{id}}>'.
				 		'<div class=fl_image></div>'.
				 		'<div class=fl_name>{{name}} ({{size}})</div>'.
				 		'<div class=fl_progress><div class=progress><div class=bar></div></div></div>'.
				 		'<div class=fl_link></div>'.
				 	'</li>'.
				 '</script>';
		
		}
		
		/**
			* Display a form to edit an album picture
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$permalink]
			* @param	string	[$description]
			* @param	integer	[$aid] Album id
		*/
		
		public static function edit_picture($id, $name, $permalink, $description, $aid){
			
			echo '<div id="edit_album_pic_goback">'.
				 	'<a class="button" href="'.Url::_(array('ns' => 'media', 'ctl' => 'editalbum'), array('id' => $aid)).'">'.Lang::_('Go Back').'</a>'.
				 '</div>';
			
			Edit::image($id, $name, $permalink, $description);
		
		}
	
	}

?>