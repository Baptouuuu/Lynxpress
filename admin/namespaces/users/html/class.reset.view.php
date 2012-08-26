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
		* Views for reset controller
		*
		* @package		Admin
		* @subpackage	Users\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @absract
	*/
	
	abstract class Reset extends Master{
	
		/**
			* Display form to reset password
			*
			* @static
			* @access	public
			* @param	string	[$action_msg]
		*/
		
		public static function reset_form($action_msg){
		
			echo '<div id="reset_form">'.
					'<div id="login_logo">'.
						'<img src="'.WS_URL.'images/lynxpress_shadow.png" alt="Lynxpress" />'.
					'</div>'.
				 	'<div class="rf_box">'.
				 		'<h2>'.Lang::_('Reset Password', 'users').'</h2>'.
				 		$action_msg.
				 		'<input class="input" type="email" name="email" placeholder="'.Lang::_('E-mail').'" required /><br/>'.
				 		'<input class="button" type="submit" name="update" value="'.Lang::_('Send password', 'users').'" />'.
				 	'</div>'.
				 	'<div class="rf_box">'.
				 		'<a href="'.Url::_(array('ns' => 'session', 'ctl' => 'login')).'">'.Lang::_('Connection', 'session').'</a>'.
				 	'</div>'.
				 '</div>';
		
		}
		
		/**
			* Message displayed went email sent after password reset
			*
			* @static
			* @access	public
		*/
		
		public static function sent(){
		
			echo '<div id="reset_form">'.
					'<div id="login_logo">'.
						'<img src="'.WS_URL.'images/lynxpress_shadow.png" alt="Lynxpress" />'.
					'</div>'.
				 	'<div class="rf_box">'.
				 		'<h2>'.Lang::_('Reset Password', 'users').'</h2>'.
				 		'<div class="message good">'.
				 			Lang::_('New password sent to your e-mail address', 'users').
				 		'</div>'.
				 	'</div>'.
				 	'<div class="rf_box">'.
				 		'<a href="'.Url::_(array('ns' => 'session', 'ctl' => 'login')).'">'.Lang::_('Connection', 'session').'</a>'.
				 	'</div>'.
				 '</div>';
		
		}
	
	}

?>