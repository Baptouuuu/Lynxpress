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
	
	namespace Admin\Update\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Update\Html
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
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'update')).'">'.Lang::_('Update').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display a form to update the website
			*
			* @static
			* @access	public
		*/
		
		public static function update(){
		
			echo '<h2>'.Lang::_('Click the button below to update "%name"', 'update', array('name' => WS_NAME)).'</h2>'.
				 '<input class="button button_publish" type=submit name=update value="'.Lang::_('Update').'" />'.
				 '<p class=indication>'.
				 	'('.Lang::_('Before update, please backup your website files to prevent any problem', 'update').'. '.Lang::_('A database backup will be made and sent to %email', 'update', array('email' => WS_EMAIL)).')<br/>'.
				 	'('.Lang::_('Please don\'t leave this page while updating', 'update').'.)'.
				 '</p>';
		
		}
	
	}

?>