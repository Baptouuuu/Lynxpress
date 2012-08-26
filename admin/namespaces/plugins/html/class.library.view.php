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
		* Views for library controller
		*
		* @package		Admin
		* @subpackage	Plugins\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Library extends Master{
	
		/**
			* Display the menu
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'library')).'">'.Lang::_('Library').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'plugins')).'">'.Lang::_('Plugins').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display a header to tell what this page is about and a search form
			*
			* @static
			* @access	public
		*/
		
		public static function actions(){
		
			echo '<h2>'.Lang::_('Official plugins library', 'plugins').'</h2>'.
				 '<div id=actions>'.
					 '<div id=search_box>'.
					 	'<input id="search_input" class="input" type="text" name="search" list="titles" placeholder="'.Lang::_('Plugins').'" x-webkit-speech />'.
					 	'<input class="button" type="submit" name="search_button" value="'.Lang::_('Search').'" />'.
					 '</div>'.
				 '</div>';
		
		}
		
		/**
			* Display plugins labels list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function plugins($part){
		
			if($part == 'o')
				echo '<section id=plugins class="labels library">';
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
			* @param	string	[$description]
			* @param	integer	[$downloaded]
		*/
		
		public static function tpl($id, $name, $description, $downloaded){
		
			echo '<div id=plg_'.$id.' class=label>'.
					'<div class=content>'.
						'<span class=label>'.Lang::_('Name').':</span> '.$name.'<br/>'.
						'<span class=label>'.Lang::_('Downloaded', 'plugins').':</span> '.$downloaded.'<br/>'.
						'<p class=txta>'.$description.'</p>'.
						'<div class=row_actions>'.
							'<a href="http://extend.lynxpress.org/?ns=plugins&id='.$id.'" target=_blank>'.Lang::_('View').'</a> | '.
							'<a class=green href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'library'), array('action' => 'install', 'id' => $id)).'">'.Lang::_('Install').'</a>'.
						'</div>'.
					'</div>'.
				 '</div>';
		
		}
		
		/**
			* Message displayed when no templates found
			*
			* @static
			* @access	public
		*/
		
		public static function no_plg(){
		
			echo Lang::_('No plugins found in the library', 'plugins');
		
		}
	
	}

?>