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
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Plugins\Html
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
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'library')).'">'.Lang::_('Library').'</a></span>'.
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'plugins')).'">'.Lang::_('Plugins').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display a button to delete plugins
			*
			* @static
			* @access	public
		*/
		
		public static function actions(){
		
			echo '<div id=actions>'.
					'<input class="button delete" type="submit" name="delete" value="'.Lang::_('Delete').'" data-confirm="'.Lang::_('Really').'?" />'.
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
				echo '<section id=plugins class=labels>';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display a plugin label
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$author]
			* @param	string	[$url]
		*/
		
		public static function plugin($id, $name, $author, $url){
		
			echo '<div id=label_'.$id.' class=label>'.
					'<label for=plg_'.$id.'>'.
						'<div class=check_label>'.
							'<input id=plg_'.$id.' type=checkbox name="plugin_id[]" value="'.$id.'" />'.
						'</div>'.
					'</label>'.
					'<div class=content>'.
						'<span class=label>'.Lang::_('Name').':</span> '.$name.'<br/>'.
						'<span class=label>'.Lang::_('Author').':</span> '.$author.'<br/>'.
						'<span class=label>'.Lang::_('URL').':</span> <a href="'.$url.'" target=_blank>'.$url.'</a>'.
					'</div>'.
				 '</div>';
		
		}
		
		/**
			* Message displayed if no plugins installed
			*
			* @static
			* @access	public
		*/
		
		public static function no_plugins(){
		
			echo Lang::_('No plugins installed! You can find some %here', 'plugins', array('here' => '<a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'library')).'">'.Lang::_('here').'</a>'));
		
		}
	
	}

?>