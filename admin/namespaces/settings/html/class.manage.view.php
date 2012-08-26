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
	
	namespace Admin\Settings\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Settings\Html
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
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display all settings sections
			*
			* @static
			* @access	public
		*/
		
		public static function settings(){
		
			echo '<section id=settings class=labels>'.
					'<div id=sl_categories class="label mini">'.
						'<div class=check_label></div>'.
						'<div class=content>'.
							'<a href="'.Url::_(array('ns' => 'categories')).'">'.Lang::_('Categories').'</a>'.
						'</div>'.
					'</div>'.
					'<div id=sl_users class="label mini">'.
						'<div class=check_label></div>'.
						'<div class=content>'.
							'<a href="'.Url::_(array('ns' => 'users')).'">'.Lang::_('Users').'</a>'.
						'</div>'.
					'</div>'.
					'<div id=sl_social class="label mini">'.
						'<div class=check_label></div>'.
						'<div class=content>'.
							'<a href="'.Url::_(array('ns' => 'social')).'">'.Lang::_('Social Buttons').'</a>'.
						'</div>'.
					'</div>'.
					'<div id=sl_homepage class="label mini">'.
						'<div class=check_label></div>'.
						'<div class=content>'.
							'<a href="'.Url::_(array('ns' => 'homepage')).'">'.Lang::_('Homepage').'</a>'.
						'</div>'.
					'</div>'.
					'<div id=sl_templates class="label mini">'.
						'<div class=check_label></div>'.
						'<div class=content>'.
							'<a href="'.Url::_(array('ns' => 'templates')).'">'.Lang::_('Templates').'</a>'.
						'</div>'.
					'</div>'.
					'<div id=sl_plugins class="label mini">'.
						'<div class=check_label></div>'.
						'<div class=content>'.
							'<a href="'.Url::_(array('ns' => 'plugins')).'">'.Lang::_('Plugins').'</a>'.
						'</div>'.
					'</div>'.
					'<div id=sl_links class="label mini">'.
						'<div class=check_label></div>'.
						'<div class=content>'.
							'<a href="'.Url::_(array('ns' => 'links')).'">'.Lang::_('Links').'</a>'.
						'</div>'.
					'</div>'.
					'<div id=sl_activity class="label mini">'.
						'<div class=check_label></div>'.
						'<div class=content>'.
							'<a href="'.Url::_(array('ns' => 'activity')).'">'.Lang::_('Activity').'</a>'.
						'</div>'.
					'</div>'.
					'<div id=sl_update class="label mini">'.
						'<div class=check_label></div>'.
						'<div class=content>'.
							'<a href="'.Url::_(array('ns' => 'update')).'">'.Lang::_('Update').'</a>'.
						'</div>'.
					'</div>'.
				 '</section>';
		
		}
	
	}

?>