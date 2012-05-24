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
	
	namespace Admin\Activity\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Activity\Html
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
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'activity')).'">'.Lang::_('Activity').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display activity table structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function table($part){
		
			if($part == 'o'){
			
				echo '<input class="button delete" type=submit name=delete value="'.Lang::_('Delete').'" />'.
					 '<table id=table>'.
						'<thead>'.
							'<tr>'.
								'<th class=column_user>'.Lang::_('User').'</th>'.
								'<th class=column_message>'.Lang::_('Message').'</th>'.
								'<th class=column_date>'.Lang::_('Date').'</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class=column_user>'.Lang::_('User').'</th>'.
								'<th class=column_message>'.Lang::_('Message').'</th>'.
								'<th class=column_date>'.Lang::_('Date').'</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Display an activity row in table
			*
			* @static
			* @access	public
			* @param	string	[$username]
			* @param	string	[$email]
			* @param	string	[$message]
			* @param	string	[$date]
		*/
		
		public static function activity($username, $email, $message, $date){
		
			echo '<tr>'.
					'<td class=column_user>'.
						$username.' <span class=indication>(<a href="mailto:'.$email.'">'.$email.'</a>)</span>'.
					'</td>'.
					'<td class=column_message>'.
						$message.
					'</td>'.
					'<td class=column_date>'.
						date('d/m/Y @ H:i:s', strtotime($date)).
					'</td>'.
				 '</tr>';
		
		}
	
	}

?>