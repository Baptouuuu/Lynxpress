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
		* Views for videos controller
		*
		* @package		Admin
		* @subpackage	Network\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Videos extends Master{
	
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
			* Display videos list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function videos($part){
		
			if($part == 'o')
				echo '<section id=network_videos>';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display a video in the list
			*
			* @static
			* @access	public
			* @param	string			[$name]
			* @param	object			[$user]
			* @param	string			[$permalink]
			* @param	string			[$description]
			* @param	string			[$date]
			* @param	object|boolean	[$fallback]
			* @param	string			[$ws_url]
		*/
		
		public static function video($name, $user, $permalink, $description, $date, $fallback, $ws_url){
		
			echo '<figure>'.
					'<video src="'.$ws_url.$permalink.'" preload=metadata controls width=640>'.
						(($fallback !== false)?$fallback->_embed_code:'').
					'</video>'.
					'<figcaption>'.
						'<details>'.
							'<summary>'.
								'"'.$name.'" '.Lang::_('is a video made by', 'network').' <span>'.$user->_publicname.'</span>'.
							'</summary>'.
							'<p>'.
								nl2br($description).
							'</p>'.
						'</details>'.
					'</figcaption>'.
				 '</figure>';
		
		}
	
	}

?>