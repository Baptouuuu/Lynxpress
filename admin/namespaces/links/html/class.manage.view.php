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
	
	namespace Admin\Links\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Links\Html
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
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'links', 'ctl' => 'edit'), array('action' => 'create')).'">'.Lang::_('Add').'</a></span>'.
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'links')).'">'.Lang::_('Links').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display links actions
			*
			* @static
			* @access	public
		*/
		
		public static function actions(){
		
			echo '<div id="actions">'.
					'<input class="button delete" type="submit" name="delete" value="'.Lang::_('Delete').'" /> &nbsp;'.
					'<select name="priority">'.
						'<option value="no">'.Lang::_('Change priority to', 'links').'...</option>'.
						'<option value="1">'.Lang::_('Very High', 'links').'</option>'.
						'<option value="2">'.Lang::_('High', 'links').'</option>'.
						'<option value="3">'.Lang::_('Normal', 'links').'</option>'.
						'<option value="4">'.Lang::_('Low', 'links').'</option>'.
						'<option value="5">'.Lang::_('Very Low', 'links').'</option>'.
					'</select>'.
					'<input class="button" type="submit" name="apply" value="'.Lang::_('Apply').'" />'.
					'<div id="search_box">'.
						'<input id="search_input" class="input" type="text" name="search" list="titles" placeholder="'.Lang::_('Links').'" />'.
						'<input class="button" type="submit" name="search_button" value="'.Lang::_('Search').'" />'.
					'</div>'.
				 '</div>';
		
		}
		
		/**
			* Display links table structure
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
								'<th class="column_checkbox"><input class="check_all" data-select="link_id" type="checkbox" /></th>'.
								'<th class="column_name">'.Lang::_('Name').'</th>'.
								'<th class="column_link">'.Lang::_('Link').'</th>'.
								'<th class="column_rss">'.Lang::_('RSS').'</th>'.
								'<th class="column_notes">'.Lang::_('Notes', 'links').'</th>'.
								'<th class="column_priority">'.Lang::_('Priority').'</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_checkbox"><input class="check_all" data-select="link_id" type="checkbox" /></th>'.
								'<th class="column_name">'.Lang::_('Name').'</th>'.
								'<th class="column_link">'.Lang::_('Link').'</th>'.
								'<th class="column_rss">'.Lang::_('RSS').'</th>'.
								'<th class="column_notes">'.Lang::_('Notes', 'links').'</th>'.
								'<th class="column_priority">'.Lang::_('Priority').'</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Display la link row in table
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$link]
			* @param	string	[$rss]
			* @param	string	[$notes]
			* @param	string	[$priority]
		*/
		
		public static function link($id, $name, $link, $rss, $notes, $priority){
		
			echo '<tr>'.
					'<th class=column_checkbox>'.
						'<input class=link_id type="checkbox" name="link_id[]" value='.$id.' />'.
					'</th>'.
					'<td class=column_name>'.
						$name.
						'<div class=row_actions>'.
							'<a href="'.Url::_(array('ns' => 'links', 'ctl' => 'edit'), array('id' => $id)).'">'.Lang::_('Edit').'</a> | '.
							'<a class=red href="'.Url::_(array('ns' => 'links'), array('action' => 'delete', 'id' => $id)).'">'.Lang::_('Delete').'</a>'.
						'</div>'.
					'</td>'.
					'<td class=column_link>'.
						'<a href="'.$link.'" target=_blank>'.
							$link.
						'</a>'.
					'</td>'.
					'<td class=column_rss>'.
						'<a href="'.$rss.'" target=_blank>'.
							$rss.
						'</a>'.
					'</td>'.
					'<td class=column_notes>'.
						'<p>'.$notes.'</p>'.
					'</td>'.
					'<td class=column_priority>'.
						$priority.
					'</td>'.
				 '</tr>';
		
		}
		
		/**
			* Message displayed if no link retrieved
			*
			* @static
			* @access	public
		*/
		
		public static function no_link(){
		
			echo '<tr><td colspan=6>'.Lang::_('No links found', 'links').'</td></tr>';
		
		}
	
	}

?>