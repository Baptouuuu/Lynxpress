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
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Users\Html;
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
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
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'users')).'">'.Lang::_('Users').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'roles')).'">'.Lang::_('Roles').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display a role in submenu
			*
			* @static
			* @access	public
			* @param	string	[$role]
			* @param	integer	[$count]
			* @param	boolean	[$selected]
		*/
		
		public static function role($role, $count, $selected){
		
			echo '<span><a '.(($selected)?'class="selected"':'').' href="'.Url::_(array('ns' => 'users'), array('role' => $role)).'">'.ucfirst($role).'</a> ('.$count.')</span>';
		
		}
		
		/**
			* Display users actions
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function actions($part){
		
			if($part == 'o'){
			
				echo '<div id="actions">'.
						'<input class="button delete" type="submit" name="delete" value="'.Lang::_('Delete').'" data-confirm="'.Lang::_('Really').'?" /> &nbsp;&nbsp;'.
						'<select name="change">'.
							'<option value="no">'.Lang::_('Change role to', 'users').'...</option>';
			
			}elseif($part == 'c'){
			
				echo	'</select> '.
						'<input class="button" type="submit" name="apply" value="'.Lang::_('Apply').'" />'.
						'<div id="search_box">'.
							'<input id="search_input" class="input" type="text" name="search" list="titles" placeholder="'.Lang::_('Users').'" x-webkit-speech />'.
							'<input class="button" type="submit" name="search_button" value="'.Lang::_('Search').'" />'.
						'</div>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display userss list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function users($part){
		
			if($part == 'o')
				echo '<section id="users" class="labels">';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display a user label
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$username]
			* @param	string	[$publicname]
			* @param	string	[$email]
			* @param	string	[$role]
			* @param	integer	[$user_id] Current user id
		*/
		
		public static function user($id, $username, $publicname, $email, $role, $user_id){
		
			echo '<div id="label_'.$id.'" class="label">'.
				 	'<label for="user_'.$id.'">'.
				 		'<div class="check_label">'.
				 			'<input id="user_'.$id.'" type="checkbox" name="user_id[]" value="'.$id.'">'.
				 		'</div>'.
				 	'</label>'.
				 	'<div class="avatar">'.
				 		'<a href="'.Url::_((($id == $user_id)?array('ns' => 'users', 'ctl' => 'profile'):array('ns' => 'users', 'ctl' => 'edit')), (($id != $user_id)?array('id' => $id):array())).'">'.
				 			'<img src="http://www.gravatar.com/avatar/'.md5(strtolower($email)).'?s=75" alt="avatar" />'.
				 		'</a>'.
				 	'</div>'.
				 	'<div class="content">'.
				 		'<span class=label>'.Lang::_('Username', 'users').':</span> '.$username.'<br/>'.
				 		'<span class=label>'.Lang::_('Publicname', 'users').':</span> '.$publicname.'<br/>'.
				 		'<span class=label>'.Lang::_('Role').':</span> '.ucfirst($role).'<br/>'.
				 		'<a href="mailto:'.$email.'">'.Lang::_('E-mail').'</a>'.
				 	'</div>'.
				 '</div>';
		
		}
		
		/**
			* Message displayed if no users
			*
			* @static
			* @access	public
		*/
		
		public static function no_user(){
		
			echo 'No users found';
		
		}
	
	}

?>