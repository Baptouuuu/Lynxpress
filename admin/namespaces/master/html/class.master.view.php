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
	
	namespace Admin\Master\Html;
	use \Library\Variable\Get as VGet;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Html master class
		*
		* Contains recurrent fonctions
		*
		* @package		Admin
		* @subpackage	Master\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.1
		* @abstract
	*/
	
	abstract class Master{
	
		/**
			* Display form tag
			*
			* @static
			* @access	public
			* @param	string [$part] $part can only contain "o" or "c"
			* @param	string [$method] Method to transmit data, can be "get" or "post"
			* @param	string [$action] Destination page to transmit data
			* @param	boolean [$enctype] If the form will upload files or not
		*/
		
		public static function form($part, $method = '', $action = '', $enctype = false){
		
			if($part == 'o'){
			
				echo '<form method="'.$method.'" action="'.$action.'" accept-charset="utf-8" '.(($enctype)?'enctype="multipart/form-data"':'').'>';
			
			}elseif($part == 'c'){
			
				echo '</form>';
			
			}
		
		}
		
		/**
			* Display a pagination, for listings controllers
			*
			* @static
			* @access	public
			* @param	integer [$p] Current page
			* @param	integer [$max] Maximum pages available
			* @param	array [$links] Additional GET parameter, if in a search
		*/
		
		public static function pagination($p, $max, array $links = array()){
		
			echo '<div id="pagination">';
			
			if($p < $max)
				echo '<a class="button" href="'.Url::_(array('ns' => VGet::ns('dashboard'), 'ctl' => VGet::ctl('manage')), $links).((empty($links))?'?':'&').'p='.($p+1).'">'.Lang::_('Previous').'</a>';
			
			if($p > 1)
				echo '<a class="button" href="'.Url::_(array('ns' => VGet::ns('dashboard'), 'ctl' => VGet::ctl('manage')), $links).((empty($links))?'?':'&').'p='.($p-1).'">'.Lang::_('Next').'</a>';
			
			echo '</div>';
		
		}
		
		/**
			* Display an error message if javascript not loaded
			*
			* @static
			* @access	public
		*/
		
		public static function noscript(){
		
			echo '<noscript><div class="message wrong">'.Lang::_('Lynxpress needs Javascript to be enabled!').'</div></noscript>';
		
		}
		
		/**
			* Display an option for a select tag
			*
			* @static
			* @access	public
			* @param	string [$key] Data submitted with the form
			* @param	string [$value] Data displayed to the user
			* @param	boolean [$selected] If the option has to be selected
		*/
		
		public static function option($key, $value, $selected = false){
		
			echo '<option value="'.$key.'" '.(($selected === true)?'selected':'').'>'.$value.'</option>';
		
		}
		
		/**
			* Display submenu structure
			*
			* @static
			* @access	public
			* @param	string [$part] Can be set to 'o' or 'c'
		*/
		
		public static function submenu($part){
		
			if($part == 'o'){
			
				echo '<nav class="submenu">';
			
			}elseif($part == 'c'){
			
				echo '</nav>';
			
			}
		
		}
		
		/**
			* Display a button to clear localStorage
			*
			* @static
			* @access	public
		*/
		
		public static function clear_localstorage(){
		
			return '<input class="button clear" type="reset" value="'.Lang::_('Clear Local Storage').'" />';
		
		}
		
		/**
			* Return a message if call to undefined method in static context
			*
			* @static
			* @access	public
			* @param	string	[$name] Method name
			* @param	array	[$arguments] Array of all arguments passed to the unknown method
			* @return	string	Error message
		*/
		
		public static function __callStatic($name, $arguments){
		
			return 'The lynx didn\'t show up calling '.$name;
		
		}
	
	}

?>