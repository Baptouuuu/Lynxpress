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
	
	namespace Admin\Links\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for edit controller
		*
		* @package		Admin
		* @subpackage	Links\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Edit extends Master{
	
		/**
			* Display the menu
			*
			* @static
			* @access	public
			* @param	boolean	[$create]
		*/
		
		public static function menu($create){
		
			echo '<div id="menu">'.
					'<span '.(($create)?'id=menu_selected':'').' class=menu_item><a href="'.Url::_(array('ns' => 'links', 'ctl' => 'edit'), array('action' => 'create')).'">'.Lang::_('Add').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'links')).'">'.Lang::_('Links').'</a></span>'.
					(($create === false)?'<span id=menu_selected class=menu_item><a href=#>'.Lang::_('Edition').'</a></span>':'').
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display form to edit a link
			*
			* @static
			* @access	public
			* @param	string	[$name]
			* @param	string	[$link]
			* @param	string	[$rss]
			* @param	string	[$notes]
			* @param	integer	[$priority]
			* @param	string	[$action] Action to do, can be create or update
		*/
		
		public static function link($name, $link, $rss, $notes, $priority, $action){
		
			echo '<section id=edit_link>'.
					'<label for=el_name>'.Lang::_('Name').'</label> <input id=el_name class=input type=text name=name value="'.$name.'" required /><br/>'.
					'<label for=el_link>'.Lang::_('Link').'</label> <input id=el_link class=input type=url name=link value="'.$link.'" required /><br/>'.
					'<label for=el_rss>'.Lang::_('RSS').'</label> <input id=el_rss class=input type=url name=rss value="'.$rss.'" /><br/>'.
					'<label for=el_notes>'.Lang::_('Notes', 'links').'</label> <textarea id=el_notes class=txta name=notes>'.$notes.'</textarea><br/>'.
					'<label for=el_priority>'.Lang::_('Priority', 'links').'</label> <select id=el_priority name=priority>'.
						'<option value="1" '.(($priority == 1)?'selected':'').'>'.Lang::_('Very High', 'links').'</option>'.
						'<option value="2" '.(($priority == 2)?'selected':'').'>'.Lang::_('High', 'links').'</option>'.
						'<option value="3" '.(($priority == 3)?'selected':'').'>'.Lang::_('Normal', 'links').'</option>'.
						'<option value="4" '.(($priority == 4)?'selected':'').'>'.Lang::_('Low', 'links').'</option>'.
						'<option value="5" '.(($priority == 5)?'selected':'').'>'.Lang::_('Very Low', 'links').'</option>'.
					'</select><br/>'.
					'<input class="button publish" type=submit name='.$action.' value="'.(($action == 'update')?Lang::_('Update'):Lang::_('Add')).'" />'.
				 '</section>';
		
		}
	
	}

?>