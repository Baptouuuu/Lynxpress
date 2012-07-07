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
	
	namespace Admin\Users\Html;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for edit controller
		*
		* @package		Admin
		* @subpackage	Users\Html;
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Edit extends Profile{
	
		/**
			* Display the menu of edit page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'users', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'users')).'">'.Lang::_('Users').'</a></span>'.
					'<span id="menu_selected" class="menu_item"><a href="#">'.Lang::_('Editing').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'roles')).'">'.Lang::_('Roles').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display roles selection
			*
			* @static
			* @access	public
			* @param	string	[$role] User role
			* @param	array	[$roles] Roles available
		*/
		
		public static function roles($role, $roles){
		
			echo '<section id="profile_roles">'.
					'<h2>'.Lang::_('Role').'</h2>'.
					'<div class="block">'.
						'<label for="role">'.Lang::_('User role', 'users').'</label> <select id="role" name="role">';
			
						if(!empty($roles))
							foreach($roles as $key => $r)
								parent::option($key, ucfirst($r->_name), (($key == $role)?true:false));
			
			echo		'</select>'.
					'</div>'.
					'<input class="button publish" type="submit" name="update" value="'.Lang::_('Update').'" /> '.
					'<input class="button delete" type="submit" name="delete" value="'.Lang::_('Delete').'" />'.
				 '</section>';
		
		}
	
	}

?>