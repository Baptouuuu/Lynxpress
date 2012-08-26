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
	
	namespace Admin\Categories\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Categories\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
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
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'categories')).'">'.Lang::_('Categories').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display a form to create a new category
			*
			* @static
			* @access	public
		*/
		
		public static function actions(){
		
			echo '<div id="actions">'.
					'<input id="new_category" class="input" type="text" name="new_category" placeholder="'.Lang::_('Category').'" /> '.
					'<select name="type">'.
						'<option value="album">'.Lang::_('Album').'</option>'.
						'<option value="post">'.Lang::_('Post').'</option>'.
						'<option value="video">'.Lang::_('Video').'</option>'.
					'</select> '.
					'<input class="button" type="submit" name="create" value="'.Lang::_('Add').'" />'.
				 '</div>';
		
		}
		
		/**
			* Display categories table structure
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
								'<th class="column_checkbox"><input class="check_all" data-select="category_id" type="checkbox" /></th>'.
								'<th class="column_name">'.Lang::_('Name').'</th>'.
								'<th class="column_type">'.Lang::_('Type').'</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_checkbox"><input class="check_all" data-select="category_id" type="checkbox" /></th>'.
								'<th class="column_name">'.Lang::_('Name').'</th>'.
								'<th class="column_type">'.Lang::_('Type').'</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>'.
					 '<input class="button delete" type="submit" name="delete" value="'.Lang::_('Delete').'" data-confirm="'.Lang::_('Really').'?" />';
			
			}
		
		}
		
		/**
			* Display a category row in tha table
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$type]
		*/
		
		public static function category($id, $name, $type){
		
			echo '<tr>'.
					'<th class="column_checkbox">'.
						'<input class="category_id" type="checkbox" name="category_id[]" value="'.$id.'" />'.
					'</th>'.
					'<td class="column_name">'.
						ucfirst($name).
						'<div class="row_actions">'.
							'<a class="red" href="'.Url::_(array('ns' => 'categories'), array('action' => 'delete', 'id' => $id)).'">'.Lang::_('Delete').'</a>'.
						'</div>'.
					'</td>'.
					'<td class="column_type">'.
						ucfirst($type).
					'</td>'.
				 '</tr>';
		
		}
	
	}

?>