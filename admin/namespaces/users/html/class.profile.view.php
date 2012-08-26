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
		* Views for profile controller
		*
		* @package		Admin
		* @subpackage	Users\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Profile extends Master{
	
		/**
			* Display the menu of profile page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'users', 'ctl' => 'profile')).'">'.Lang::_('Profile').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display profile form
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$username]
			* @param	string	[$nickname]
			* @param	string	[$firstname]
			* @param	string	[$lastname]
			* @param	string	[$publicname]
			* @param	string	[$email]
			* @param	string	[$website]
			* @param	string	[$msn]
			* @param	string	[$twitter]
			* @param	string	[$facebook]
			* @param	string	[$google]
			* @param	string	[$bio]
		*/
		
		public static function profile($id, $username, $nickname, $firstname, $lastname, $publicname, $email, $website, $msn, $twitter, $facebook, $google, $bio){
		
			echo '<section id="profile">'.
					'<div id="avatar">'.
						'<img src="http://www.gravatar.com/avatar/'.md5(strtolower($email)).'?s=50" alt="avatar" />'.
					'</div>'.
					'<h2>'.Lang::_('Name').'</h2>'.
					'<div class="block">'.
						'<label>'.Lang::_('Username', 'users').'</label> <input id="username" class="input" type="text" value="'.$username.'" disabled /><br/>'.
						'<label for="fn">'.Lang::_('First Name', 'users').'</label> <input id="fn" class="input" type="text" name="firstname" value="'.$firstname.'" /><br/>'.
						'<label for="ln">'.Lang::_('Last Name', 'users').'</label> <input id="ln" class="input" type="text" name="lastname" value="'.$lastname.'" /><br/>'.
						'<label for="nn">'.Lang::_('Nickname', 'users').'</label> <input id="nn" class="input" type="text" name="nickname" value="'.$nickname.'" /><br/>'.
						'<label for="pn">'.Lang::_('Publicname', 'users').'</label> <select id="pn" name="publicname">'.self::publicname($username, $firstname, $lastname, $nickname, $publicname).'</select><br/>'.
					'</div>'.
					'<h2>'.Lang::_('Contact').'</h2>'.
					'<div class="block">'.
						'<label for="email">'.Lang::_('E-mail').'</label> <input id="email" class="input" type="email" name="email" value="'.$email.'" required /><br/>'.
						'<label for="website">'.Lang::_('Website', 'users').'</label> <input id="website" class="input" type="url" name="website" value="'.$website.'" /><br/>'.
						'<label for="msn">'.Lang::_('MSN', 'users').'</label> <input id="msn" class="input" type="email" name="msn" value="'.$msn.'" /><br/>'.
						'<label for="twitter">Twitter</label> <input id="twitter" class="input" type="url" name="twitter" value="'.$twitter.'" /><br/>'.
						'<label for="facebook">Facebook</label> <input id="facebook" class="input" type="url" name="facebook" value="'.$facebook.'" /><br/>'.
						'<label for="google">Google+</label> <input id="google" class="input" type="url" name="google" value="'.$google.'" /><br/>'.
					'</div>'.
					'<h2>'.Lang::_('Privacy', 'users').'</h2>'.
					'<div class="block">'.
						'<label for="bio">'.Lang::_('Biography', 'users').'</label> <textarea id="bio" class=txta name="bio" placeholder="'.Lang::_('Share a bit about yourself', 'users').'">'.$bio.'</textarea><br/>'.
						'<label for="old_pwd">'.Lang::_('Old Password', 'users').'</label> <input id="old_pwd" class="input" type="password" name="old_pwd" placeholder="'.Lang::_('Old Password', 'users').'" /><br/>'.
						'<label for="pwd">'.Lang::_('New Password', 'users').'</label> <input id="pwd" class="input" type="password" name="pwd" placeholder="'.Lang::_('New Password', 'users').'" /><br/>'.
						'<input id="re_pwd" class="input" type="password" name="re_pwd" placeholder="'.Lang::_('Re-type new password', 'users').'" /><br/>'.
					'</div>'.
					'<input class="button publish" type="submit" name="update" value="'.Lang::_('Update').'" /> '.
				 '</section>';
		
		}
		
		/**
			* Return all options available for publicname choose
			*
			* @static
			* @access	private
			* @param	string	[$username]
			* @param	string	[$firstname]
			* @param	string	[$lastname]
			* @param	string	[$nickname]
			* @param	string	[$publicname]
			* @return	string
		*/
		
		private static function publicname($username, $firstname, $lastname, $nickname, $publicname){
		
			$return = null;
			
			$return .= '<option '.(($publicname == $username)?'selected':'').'>'.$username.'</option>';
			
			if(!empty($firstname))
				$return .= '<option id="opt_fn" '.(($publicname == $firstname)?'selected':'').'>'.$firstname.'</option>';
			
			if(!empty($lastname))
				$return .= '<option id="opt_ln" '.(($publicname == $lastname)?'selected':'').'>'.$lastname.'</option>';
			
			if(!empty($firstname) && !empty($lastname)){
			
				$return .= '<option id="opt_lnfn" '.(($publicname == $lastname.' '.$firstname)?'selected':'').'>'.$lastname.' '.$firstname.'</option>';
				$return .= '<option id="opt_fnln" '.(($publicname == $firstname.' '.$lastname)?'selected':'').'>'.$firstname.' '.$lastname.'</option>';
			
			}
			
			if(!empty($nickname))
				$return .= '<option id="opt_nn" '.(($publicname == $nickname)?'selected':'').'>'.$nickname.'</option>';
			
			return $return;
		
		}
	
	}

?>