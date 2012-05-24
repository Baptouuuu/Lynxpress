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
		* Views for add controller
		*
		* @package		Admin
		* @subpackage	Users\Html;
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Add extends Master{
		
		/**
			* Display the menu of add page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'users', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'users')).'">'.Lang::_('Users').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'roles')).'">'.Lang::_('Roles').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display user creation form
			*
			* @static
			* @access	public
			* @param	string	[$username]
			* @param	string	[$email]
			* @param	string	[$role]
			* @param	array	[$roles]
		*/
		
		public static function user($username, $email, $role, $roles){
		
			echo '<section id="add_user">'.
				 	'<div class="block">'.
				 		'<label for="uusername">'.Lang::_('Username', 'users').'</label> <input id="uusername" class="input" type="text" name="username" value="'.$username.'" required /><br/>'.
				 		'<label for="uemail">'.Lang::_('E-mail').'</label> <input id="uemail" class="input" type="email" name="email" value="'.$email.'" required /><br/>'.
				 		'<label for="pwd">'.Lang::_('Password').'</label> <input id="pwd" class="input" type="password" name="pwd" placeholder="'.Lang::_('Password').'" required /><br/>'.
				 		'<input id="re_pwd" class="input" type="password" name="re_pwd" placeholder="'.Lang::_('Re-type password', 'users').'" required /><br/>'.
				 		'<label for="send_pwd">'.Lang::_('Send password', 'users').'?</label> <span id="send"><span><input id="send_pwd" type="radio" name="send_pwd" value="yes" checked /><label for="send_pwd">'.Lang::_('Yes').'</label></span><span><input id="not_send_pwd" type="radio" name="send_pwd" value="no" /><label for="not_send_pwd">'.Lang::_('No').'</label></span></span><br/>'.
				 		'<label for="role">'.Lang::_('Role').'<label> <select id="role" name="role">';
				 		
				 		if(!empty($roles))
				 			foreach($roles as $key => $r)
				 				parent::option($key, ucfirst($r->_name), (($key == $role)?true:false));
				 		
			echo 		'</select>'.
				 	'</div>'.
				 	'<input class="button button_publish" type="submit" name="create" value="'.Lang::_('Add').'" />'.
				 '</section>';
		
		}
	
	}

?>