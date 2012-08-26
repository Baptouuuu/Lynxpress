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
		* Manage html class contains views for manage controller
		*
		* @package		Admin
		* @subpackage	Media\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Manage extends Master{
	
		/**
			* Display the menu of media manage page
			*
			* @static
			* @access	public
			* @param	boolean [$album]
		*/
		
		public static function menu($album){
		
			echo '<div id="menu">'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'media')).'">'.Lang::_('Media').'</a></span>'.
				 	(($album)?'<span class="menu_item"><a href="'.Url::_(array('ns' => 'media', 'ctl' => 'albums')).'">'.Lang::_('Album').'</a></span>':'').
				 '</div>';
		
		}
		
		/**
			* Display submenu
			*
			* @static
			* @access	public
			* @param	string	[$type]
			* @param	string	[$display]
			* @param	int		[$count]
			* @param	boolean	[$selected]
		*/
		
		public static function submenu_type($type, $display, $count, $selected){
		
			echo '<span><a '.(($selected === true)?'class="selected"':'').' href="'.Url::_(array('ns' => 'media'), array('type' => $type)).'">'.$display.'</a> ('.$count.')</span>';
		
		}
		
		/**
			* Display actions structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
			* @param	string	[$type] Type currently viewed
		*/
		
		public static function actions($part, $type = 'image'){
		
			if($part == 'o'){
			
				echo '<div id="actions">'.
						'<input class="button delete" type="submit" name="delete" value="'.Lang::_('Delete').'" data-confirm="'.Lang::_('Really').'?" />&nbsp;&nbsp;'.
						'<select name="date">'.
							'<option value="all">'.Lang::_('Dates').'...</option>';
			
			}elseif($part == 'm'){
			
				echo	'</select> '.
						'<select name="category">'.
							'<option value="all">'.Lang::_('Categories').'...</option>';
			
			}elseif($part == 'c'){
			
				echo 	'</select> '.
						'<input class="button" type="submit" name="filter" value="'.Lang::_('Filter').'" />'.
						'<div id="search_box">'.
							'<input id="search_input" class="input" type="text" name="search" list="titles" placeholder="'.Lang::_('Media').'" x-webkit-speech />'.
							'<input class="button" type="submit" name="search_button" value="'.Lang::_('Search').'" />'.
						'</div>'.
						'<input type="hidden" name="type" value="'.$type.'" />'.
					'</div>';
			
			}
		
		}
		
		/**
			* Display medias table structure
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function table($part){
		
			if($part == 'o'){
			
				echo '<table id="table">'.
						'<thead>'.
							'<tr>'.
								'<th class="column_checkbox"><input class="check_all" data-select="media_id" type="checkbox" /></th>'.
								'<th class="column_file">'.Lang::_('File').'</th>'.
								'<th class="column_user">'.Lang::_('User').'</th>'.
								'<th class="column_media_date">'.Lang::_('Date').'</th>'.
								'<th class="column_media_links">'.Lang::_('Links').'</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_checkbox"><input class="check_all" data-select="media_id" type="checkbox" /></th>'.
								'<th class="column_file">'.Lang::_('File').'</th>'.
								'<th class="column_user">'.Lang::_('User').'</th>'.
								'<th class="column_media_date">'.Lang::_('Date').'</th>'.
								'<th class="column_media_links">'.Lang::_('Links').'</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Display a media in the table
			*
			* @static
			* @access	public
			* @param	int		[$id]
			* @param	string	[$name]
			* @param	string	[$type]
			* @param	object	[$user]
			* @param	string	[$permalink]
			* @param	string	[$embed_code]
			* @param	string	[$date]
		*/
		
		public static function media($id, $name, $type, $user, $permalink, $embed_code, $date){
		
			switch(substr($type, 0, 5)){
			
				case 'image':
					$dir = dirname($permalink).'/';
					$fname = basename($permalink);
					$label = $dir.'150-'.$fname;
					
					$links = '<input class="input" type="text" value="'.$permalink.'" readonly /><br/>'.
							 '<input class="input" type="text" value="'.$dir.'150-'.$fname.'" readonly /><br/>'.
							 '<input class="input" type="text" value="'.$dir.'300-'.$fname.'" readonly /><br/>'.
							 '<input class="input" type="text" value="'.$dir.'1000-'.$fname.'" readonly />';
					break;
				
				case 'video':
					$label = 'images/thumb_video.png';
					
					$links = '<input class="input" type="text" value="'.$permalink.'" readonly />';
					break;
				
				case 'alien':
					$label = 'images/thumb_alien.png';
					
					$links = '<textarea class="txta" readonly>'.$embed_code.'</textarea>';
					break;
			
			}
			
			echo '<tr>'.
					'<th class="column_checkbox">'.
						'<input id="media_'.$id.'" class="media_id" type="checkbox" name="media_id[]" value="'.$id.'" />'.
						'<label for="media_'.$id.'">'.
							'<img src="'.WS_URL.$label.'" alt="" />'.
						'</label>'.
					'</th>'.
					'<td class="column_file">'.
						'<p>'.$name.'</p>'.
						'<p>'.$type.'</p>'.
						'<div class="row_actions">'.
							'<a href="'.Url::_(array('ns' => 'media', 'ctl' => 'edit'), array('id' => $id)).'">'.Lang::_('Edit').'</a> | '.
							'<a class="red" href="'.Url::_(array('ns' => 'media'), array('action' => 'delete', 'id' => $id)).'">'.Lang::_('Delete').'</a>'.
						'</div>'.
					'</td>'.
					'<td class="column_user">'.
						'<a href="'.Url::_(array('ns' => 'media'), array('user' => $user->_id)).'">'.$user->_username.'</a>'.
					'</td>'.
					'<td class="column_media_date">'.
						date('d/m/Y @ H:i', strtotime($date)).
					'</td>'.
					'<td class="column_media_links">'.
						$links.
					'</td>'.
				 '</tr>';
		
		}
		
		/**
			* Displayed message if no media to display
			*
			* @static
			* @access	public
		*/
		
		public static function no_media(){
		
			echo '<tr><td colspan="5">'.Lang::_('No media to display', 'media').'</td></tr>';
		
		}
	
	}

?>