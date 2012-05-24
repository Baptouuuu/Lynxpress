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
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Templates\Html
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
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'templates', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'templates', 'ctl' => 'library')).'">'.Lang::_('Library').'</a></span>'.
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'templates')).'">'.Lang::_('Templates').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display actions available for templates and a header to display which template is currently in use
			*
			* @static
			* @access	public
			* @param	string	[$template]
		*/
		
		public static function actions($template){
		
			echo '<h2>'.Lang::_('You\'re currently using', 'templates').': "'.$template.'"</h2>'.
				 '<div id=actions>'.
				 	'<input class="button delete" type=submit name=delete value="'.Lang::_('Delete').'" /> '.
				 	'<input class=button type=submit name=update value="'.Lang::_('Use').'" />'.
				 '</div>';
		
		}
		
		/**
			* Display templates labels structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function templates($part){
		
			if($part == 'o')
				echo '<section id=templates class=labels>';
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
			* @param	string	[$author]
			* @param	string	[$url]
		*/
		
		public static function template($id, $name, $author, $url){
		
			echo '<div id=label_'.$id.' class=label>'.
					'<label for=tpl_'.$id.'>'.
						'<div class=check_label>'.
							'<input id=tpl_'.$id.' type=radio name=template value="'.$id.'" />'.
						'</div>'.
					'</label>'.
					'<div class=content>'.
						'<span class=label>'.Lang::_('Name').':</span> '.$name.'<br/>'.
						'<span class=label>'.Lang::_('Author').':</span> '.$author.'<br/>'.
						'<span class=label>'.Lang::_('Url').':</span> <a href="'.$url.'" target=_blank>'.$url.'</a>'.
					'</div>'.
				 '</div>';
		
		}
	
	}

?>