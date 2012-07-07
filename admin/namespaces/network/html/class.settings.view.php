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
	
	namespace Admin\Network\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for settings controller
		*
		* @package		Admin
		* @subpackage	Network\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Settings extends Master{
	
		/**
			* Display the menu
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'network')).'">'.Lang::_('Network').'</a></span>'.
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'network', 'ctl' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display a form to add a new website to the network
			*
			* @static
			* @access	public
		*/
		
		public static function add_form(){
		
			echo '<div id=network_add>'.
					'<input class=input type=text name=title placeholder="'.Lang::_('Website title', 'network').'" /> '.
					'<input class=input type=url name=url placeholder="http://lynxpress.org/" /> '.
					'<input class="button publish" type=submit name=create value="'.Lang::_('Add').'" />'.
				 '</div>';
		
		}
		
		/**
			* Display a button to delete websites from the network
			*
			* @static
			* @access	public
		*/
		
		public static function actions(){
		
			echo '<div id=actions>'.
					'<input class="button delete" type=submit name=delete value="'.Lang::_('Delete').'" data-confirm="'.Lang::_('Really').'?" />'.
				 '</div>';
		
		}
		
		/**
			* Display websites list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function websites($part){
		
			if($part == 'o')
				echo '<section id=network_settings class=labels>';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display a website label 
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$title]
			* @param	string	[$url]
		*/
		
		public static function website($id, $title, $url){
		
			echo '<div id=label_'.$id.' class=label>'.
					'<label for=ws_'.$id.'>'.
						'<div class=check_label>'.
							'<input id=ws_'.$id.' type=checkbox name="ws[]" value="'.$id.'" />'.
						'</div>'.
					'</label>'.
					'<div class=content>'.
						'<span class=label>'.Lang::_('Title').':</span> '.$title.'<br/>'.
						'<span class=label>'.Lang::_('URL').':</span> '.$url.
					'</div>'.
				 '</div>';
		
		}
		
		/**
			* Message displayed if no websites in the network
			*
			* @static
			* @access	public
		*/
		
		public static function no_website(){
		
			echo Lang::_('No website in your network', 'network');
		
		}
	
	}

?>