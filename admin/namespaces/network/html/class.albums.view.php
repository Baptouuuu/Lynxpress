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
		* Views for albums controller
		*
		* @package		Admin
		* @subpackage	Network\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Albums extends Master{
	
		/**
			* Display the menu
			*
			* @static
			* @access	public
			* @param	string	[$title]
		*/
		
		public static function menu($title){
		
			echo '<div id="menu">'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'network')).'">'.Lang::_('Network').'</a></span>'.
					'<span id=menu_selected class=menu_item><a href="#">'.$title.'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'network', 'ctl' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display albums list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function albums($part){
		
			if($part == 'o')
				echo '<section id=network_albums class=labels>';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display an album in the list
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	object	[$user]
			* @param	string	[$permalink]
			* @param	string	[$description]
			* @param	string	[$date]
			* @param	integer	[$website]
			* @param	string	[$ws_url]
		*/
		
		public static function album($id, $name, $user, $permalink, $description, $date, $website, $ws_url){
		
			echo '<div class=label>'.
					'<div class=thumb>'.
						'<a href="'.Url::_(array('ns' => 'network', 'ctl' => 'album'), array('ws' => $website, 'id' => $id)).'">'.
							'<img src="'.$ws_url.$permalink.'150-cover.png" alt="'.$name.'" />'.
						'</a>'.
					'</div>'.
					'<div class=content>'.
						'<span class=label>'.Lang::_('Name').':</span> '.$name.'<br/>'.
						'<span class=label>'.Lang::_('User').':</span> '.$user->_publicname.'<br/>'.
						'<span class=label>'.Lang::_('Creation').':</span> '.date('F d, Y', strtotime($date)).'<br/>'.
					'</div>'.
				 '</div>';
		
		}
	
	}

?>