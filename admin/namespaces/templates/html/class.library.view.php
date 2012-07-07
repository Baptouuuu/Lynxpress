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
	
	namespace Admin\Templates\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for library controller
		*
		* @package		Admin
		* @subpackage	Templates\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
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
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'templates', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'templates', 'ctl' => 'library')).'">'.Lang::_('Library').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'templates')).'">'.Lang::_('Templates').'</a></span>'.
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
		
			echo '<h2>'.Lang::_('Official templates library', 'templates').'</h2>'.
				 '<div id=actions>'.
					 '<div id=search_box>'.
					 	'<input id="search_input" class="input" type="text" name="search" list="titles" placeholder="'.Lang::_('Templates').'" x-webkit-speech />'.
					 	'<input class="button" type="submit" name="search_button" value="'.Lang::_('Search').'" />'.
					 '</div>'.
				 '</div>';
		
		}
		
		/**
			* Display templates labels list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function templates($part){
		
			if($part == 'o')
				echo '<section id=templates class="labels library">';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display a template label
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$description]
			* @paral	array	[$images]
			* @param	integer	[$downloaded]
		*/
		
		public static function tpl($id, $name, $description, $images, $downloaded){
		
			echo '<div id=tpl_'.$id.' class=label>'.
					'<div class=image>'.
						((!empty($images[0]))?
							'<a class=fancybox href="'.$images[0].'">'.
								'<img src="'.$images[0].'" alt="cover" />'.
							'</a>'
						:
							''
						).
					'</div>'.
					'<div class=content>'.
						'<span class=label>'.Lang::_('Name').':</span> '.$name.'<br/>'.
						'<span class=label>'.Lang::_('Downloaded', 'templates').':</span> '.$downloaded.'<br/>'.
						'<p class=txta>'.$description.'</p>'.
						'<div class=row_actions>'.
							'<a href="http://extend.lynxpress.org/?ns=templates&id='.$id.'" target=_blank>'.Lang::_('View').'</a> | '.
							'<a class=green href="'.Url::_(array('ns' => 'templates', 'ctl' => 'library'), array('action' => 'install', 'id' => $id)).'">'.Lang::_('Install').'</a>'.
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
		
		public static function no_tpl(){
		
			echo Lang::_('No templates found in the library', 'templates');
		
		}
	
	}

?>