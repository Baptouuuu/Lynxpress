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
	
	namespace Admin\Plugins\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for bridge controller
		*
		* @package		Admin
		* @subpackage	Plugins\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Bridge extends Master{
	
		/**
			* Display the menu
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'bridge')).'">'.Lang::_('Plugins').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display plugins list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function plugins($part){
		
			if($part == 'o')
				echo '<section id=plugins_bridge class=labels>';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display a plugin label
			*
			* @static
			* @access	public
			* @param	string	[$namespace]
			* @param	string	[$controller]
			* @param	string	[$name]
		*/
		
		public static function plugin($namespace, $controller, $name){
		
			echo '<div class="label mini">'.
					'<div class=content>'.
						'<a href="'.Url::_(array('ns' => $namespace, 'ctl' => $controller)).'">'.$name.'</a>'.
					'</div>'.
				 '</div>';
		
		}
		
		/**
			* Message displayed if no plugins installed
			*
			* @static
			* @access	public
		*/
		
		public static function no_plugin(){
		
			echo Lang::_('No plugins installed! You can find some %here', 'plugins', array('here' => '<a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'library')).'">'.Lang::_('here').'</a>'));
		
		}
	
	}

?>