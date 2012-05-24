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
	
	namespace Admin\HomePage\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	HomePage\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Manage extends Master{
	
		/**
			* Display the menu
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'homepage')).'">'.Lang::_('Homepage').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display form to choose homepage
			*
			* @static
			* @access	public
			* @param	object	[$setting]
			* @param	array	[$posts]
			* @param	array	[$videos]
			* @param	array	[$albums]
		*/
		
		public static function homepage($setting, $posts, $videos, $albums){
		
			echo '<h2>'.Lang::_('Homepage type', 'homepage').'</h2>'.
				 '<section id="homepage" class="labels">'.
				 	'<div class="label mini">'.
				 		'<label for="post">'.
				 			'<div class="check_label">'.
				 				'<input id="post" type="radio" name="type" value="post" '.(($setting->type == 'post')?'checked':'').' />'.
				 			'</div>'.
				 		'</label>'.
				 		'<div class="content">'.
				 			'<label for="post">'.
				 				Lang::_('Posts').
				 			'</label>'.
				 		'</div>'.
				 	'</div>'.
				 	'<div class="label mini">'.
				 		'<label for="album">'.
				 			'<div class="check_label">'.
				 				'<input id="album" type="radio" name="type" value="album" '.(($setting->type == 'album')?'checked':'').' />'.
				 			'</div>'.
				 		'</label>'.
				 		'<div class="content">'.
				 			'<label for="album">'.
				 				Lang::_('Album').
				 			'</label>'.
				 		'</div>'.
				 	'</div>'.
				 	'<div class="label mini">'.
				 		'<label for="video">'.
				 			'<div class="check_label">'.
				 				'<input id="video" type="radio" name="type" value="video" '.(($setting->type == 'video')?'checked':'').' />'.
				 			'</div>'.
				 		'</label>'.
				 		'<div class="content">'.
				 			'<label for="video">'.
				 				Lang::_('Video').
				 			'</label>'.
				 		'</div>'.
				 	'</div>'.
				 '</section>'.
				 '<h2>'.Lang::_('Select an item', 'homepage').'</h2>'.
				 '<div id="homepage_lists">'.
				 	'<select id="hl_post" name="post" '.(($setting->type == 'post')?'class="selected"':'').' >'.
				 		'<option value="all">'.Lang::_('All').'</option>';
				 		
				 		if(!empty($posts))
				 			foreach($posts as $p)
				 				parent::option($p->_id, $p->_title, (($p->_id == $setting->view)?true:false));
				 		
			echo 	'</select>'.
				 	'<select id="hl_album" name="album" '.(($setting->type == 'album')?'class="selected"':'').' >'.
				 		'<option value="all">'.Lang::_('All').'</option>';
				 		
				 		if(!empty($albums))
				 			foreach($albums as $a)
				 				parent::option($a->_id, $a->_name, (($a->_id == $setting->view)?true:false));
				 		
			echo 	'</select>'.
				 	'<select id="hl_video" name="video" '.(($setting->type == 'video')?'class="selected"':'').' >'.
				 		'<option value="all">'.Lang::_('All').'</option>';
				 		
				 		if(!empty($videos))
				 			foreach($videos as $v)
				 				parent::option($v->_id, $v->_name, (($v->_id == $setting->view)?true:false));
				 		
			echo 	'</select>'.
				 '</div>'.
				 '<input class="button button_publish" type="submit" name="update" value="'.Lang::_('Update').'" />';
		
		}
	
	}

?>