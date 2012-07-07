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
	
	namespace Admin\Social\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Social\Html
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
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'social')).'">'.Lang::_('Social Buttons').'</a></span>'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display form to choose social buttons
			*
			* @static
			* @access	public
			* @param	array	[$networks]
		*/
		
		public static function social_form($networks){
		
			echo '<h2>'.Lang::_('Check social networks you want to activate share buttons', 'social').'</h2>'.
				 '<input class="button publish" type="submit" name="update" value="'.Lang::_('Update').'" />'.
				 '<section id="social_buttons" class="labels">'.
				 	'<div class="label mini">'.
				 		'<label for="google">'.
				 			'<div class="check_label">'.
				 				'<input id="google" type="checkbox" name="networks[]" value="google" '.((in_array('google', $networks))?'checked':'').' />'.
				 			'</div>'.
				 		'</label>'.
				 		'<div class="content">'.
				 			'<label for="google">'.
				 				Lang::_('Google+', 'social').
				 			'</label>'.
				 		'</div>'.
				 	'</div>'.
				 	'<div class="label mini">'.
				 		'<label for="twitter">'.
				 			'<div class="check_label">'.
				 				'<input id="twitter" type="checkbox" name="networks[]" value="twitter" '.((in_array('twitter', $networks))?'checked':'').' />'.
				 			'</div>'.
				 		'</label>'.
				 		'<div class="content">'.
				 			'<label for="twitter">'.
				 				Lang::_('Twitter', 'social').
				 			'</label>'.
				 		'</div>'.
				 	'</div>'.
				 	'<div class="label mini">'.
				 		'<label for="facebook">'.
				 			'<div class="check_label">'.
				 				'<input id="facebook" type="checkbox" name="networks[]" value="facebook" '.((in_array('facebook', $networks))?'checked':'').' />'.
				 			'</div>'.
				 		'</label>'.
				 		'<div class="content">'.
				 			'<label for="facebook">'.
				 				Lang::_('Facebook', 'social').
				 			'</label>'.
				 		'</div>'.
				 	'</div>'.
				 '</section>';
		
		}
	
	}

?>