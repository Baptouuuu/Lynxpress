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
	
	namespace Admin\Roles\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Roles\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Manage extends Master{
	
		/**
			* Display the menu of manage page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'users', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'users')).'">'.Lang::_('Users').'</a></span>'.
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'roles')).'">'.Lang::_('Roles').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display a form to create a new role
			*
			* @static
			* @access	public
		*/
		
		public static function actions(){
		
			echo '<div id="actions">'.
					'<input id="new_role" class="input" type="text" name="new_role" placeholder="'.Lang::_('Role').'" /> '.
					'<input class="button" type="submit" name="create" value="'.Lang::_('Add').'" />'.
				 '</div>';
		
		}
		
		/**
			* Display roles table structure with a button to update data
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
								'<th class="column_role_name">'.Lang::_('Role').'</th>'.
								'<th class="column_role">'.Lang::_('Dashboard').'</th>'.
								'<th class="column_role">'.Lang::_('Posts').'</th>'.
								'<th class="column_role">'.Lang::_('Media').'</th>'.
								'<th class="column_role">'.Lang::_('Album').'</th>'.
								'<th class="column_role">'.Lang::_('Comments').'</th>'.
								'<th class="column_role">'.Lang::_('Delete').'</th>'.
								'<th class="column_role">'.Lang::_('Settings').'</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_role_name">'.Lang::_('Role').'</th>'.
								'<th class="column_role">'.Lang::_('Dashboard').'</th>'.
								'<th class="column_role">'.Lang::_('Posts').'</th>'.
								'<th class="column_role">'.Lang::_('Media').'</th>'.
								'<th class="column_role">'.Lang::_('Album').'</th>'.
								'<th class="column_role">'.Lang::_('Comments').'</th>'.
								'<th class="column_role">'.Lang::_('Delete').'</th>'.
								'<th class="column_role">'.Lang::_('Settings').'</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>'.
					 '<input class="button publish" type="submit" name="update" value="'.Lang::_('Update').'" />';
			
			}
		
		}
		
		/**
			* Display a role row in table
			*
			* @static
			* @access	public
			* @param	object	[$role]
		*/
		
		public static function role($role){
		
			echo '<tr>'.
					'<td class="column_role_name">'.
						ucfirst($role->_name).
						'<div class="row_actions">'.
							'<a class="red" href="'.Url::_(array('ns' => 'roles'), array('action' => 'delete', 'id' => $role->_id)).'">'.Lang::_('Delete').'</a>'.
						'</div>'.
					'</td>'.
					'<td class="column_role">'.
						'<input type="checkbox" name="role_'.$role->_name.'[]" value="dashboard" '.(($role->_data->dashboard)?'checked':'').' />'.
					'</td>'.
					'<td class="column_role">'.
						'<input type="checkbox" name="role_'.$role->_name.'[]" value="post" '.(($role->_data->post)?'checked':'').' />'.
					'</td>'.
					'<td class="column_role">'.
						'<input type="checkbox" name="role_'.$role->_name.'[]" value="media" '.(($role->_data->media)?'checked':'').' />'.
					'</td>'.
					'<td class="column_role">'.
						'<input type="checkbox" name="role_'.$role->_name.'[]" value="album" '.(($role->_data->album)?'checked':'').' />'.
					'</td>'.
					'<td class="column_role">'.
						'<input type="checkbox" name="role_'.$role->_name.'[]" value="comment" '.(($role->_data->comment)?'checked':'').' />'.
					'</td>'.
					'<td class="column_role">'.
						'<input type="checkbox" name="role_'.$role->_name.'[]" value="delete" '.(($role->_data->delete)?'checked':'').' />'.
					'</td>'.
					'<td class="column_role">'.
						'<input type="checkbox" name="role_'.$role->_name.'[]" value="setting" '.(($role->_data->setting)?'checked':'').' />'.
					'</td>'.
				 '</tr>';
		
		}
	
	}

?>