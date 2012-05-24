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
		* Albums html class contains views for albums controller
		*
		* @package		Admin
		* @subpackage	Media\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Albums extends Master{
	
		/**
			* Display the menu of media albums page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media')).'">'.Lang::_('Media').'</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'media', 'ctl' => 'albums')).'">'.Lang::_('Album').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display albums actions
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function actions($part){
		
			if($part == 'o'){
			
				echo '<div id="actions">'.
						'<select name="action">'.
							'<option value="no">'.Lang::_('Actions').'...</option>'.
							'<option value="publish">'.Lang::_('Publish').'</option>'.
							'<option value="unpublish">'.Lang::_('Unpublish').'</option>'.
							'<option value="delete">'.Lang::_('Delete').'</option>'.
						'</select>'.
						'<input class="button" type="submit" name="apply" value="'.Lang::_('Apply').'" />&nbsp;&nbsp;'.
						'<select name="date">'.
							'<option value="all">'.Lang::_('Dates').'...</option>';
			
			}elseif($part == 'm'){
			
				echo	'</select>'.
						'<select name="category">'.
							'<option value="all">'.Lang::_('Categories').'</option>';
			
			}elseif($part == 'c'){
			
				echo 	'</select>'.
						'<input class="button" type="submit" name="filter" value="'.Lang::_('Filter').'" />'.
						'<div id="search_box">'.
							'<input id="search_input" class="input" type="text" name="search" list="titles" placeholder="'.Lang::_('Album').'" />'.
							'<input class="button" type="submit" name="search_button" value="'.Lang::_('Search').'" />'.
						'</div>'.
					'</div>';
			
			}
		
		}
		
		/**
			* Display albums list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function albums($part){
		
			if($part == 'o'){
			
				echo '<section id="albums_labels" class="labels">';
			
			}elseif($part == 'c'){
			
				echo '</section>';
			
			}
		
		}
		
		/**
			* Display an album label
			*
			* @static
			* @access	public
			* @param	int		[$id]
			* @param	string	[$name]
			* @param	object	[$user]
			* @param	string	[$status]
			* @param	string	[$category]
			* @param	string	[$permalink]
			* @param	string	[$date]
		*/
		
		public static function album($id, $name, $user, $status, $category, $permalink, $date){
		
			echo '<div id="label_'.$id.'" class="label">'.
					'<label for="album_'.$id.'">'.
						'<div class="check_label">'.
							'<input id="album_'.$id.'" type="checkbox" name="album_id[]" value="'.$id.'" />'.
						'</div>'.
					'</label>'.
					'<div class="thumb">'.
						'<a href="'.Url::_(array('ns' => 'media', 'ctl' => 'editalbum'), array('id' => $id)).'">'.
							'<img src="'.WS_URL.$permalink.'150-cover.png" alt="'.$name.'" />'.
						'</a>'.
					'</div>'.
					'<div class="content">'.
						'<span class=label>'.Lang::_('Name').':</span> '.$name.'<br/>'.
						'<span class=label>'.Lang::_('Status').':</span> '.ucfirst($status).'<br/>'.
						'<span class=label>'.Lang::_('User').':</span> '.$user->_username.'<br/>'.
						'<span class=label>'.Lang::_('Creation').':</span> '.date('F d, Y', strtotime($date)).'<br/>'.
						'<span class=label>'.Lang::_('Categories').':</span> '.$category.''.
					'</div>'.
				 '</div>';
		
		}
		
		/**
			* Message displayed if no albums
			*
			* @static
			* @access	public
		*/
		
		public static function no_album(){
		
			echo Lang::_('No albums found! Go make some pictures then create an album', 'media').' <a href="'.Url::_(array('ns' => 'media', 'ctl' => 'add'), array('type' => 'album')).'">'.Lang::_('here').'</a>';
		
		}
	
	}

?>