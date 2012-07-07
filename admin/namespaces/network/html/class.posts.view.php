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
		* Views for posts controller
		*
		* @package		Admin
		* @subpackage	Network\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Posts extends Master{
	
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
			* Display posts list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function posts($part){
		
			if($part == 'o')
				echo '<ul id=network_posts>';
			elseif($part == 'c')
				echo '</ul>';
		
		}
		
		/**
			* Display a post in the list
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$title]
			* @param	string	[$content]
			* @param	string	[$date]
			* @param	object	[$user]
			* @param	string	[$tags]
			* @param	string	[$slug]
			* @param	integer	[$website]
		*/
		
		public static function post($id, $title, $content, $date, $user, $tags, $slug, $website){
		
			echo '<li>'.
					'<div class=title>'.
						$title.
					'</div>'.
					'<div class=side>'.
						'<div>'.
							date('d/m/Y @ H:i', strtotime($date)).
						'</div>'.
						'<div>'.
							Lang::_('Created by').' '.$user->_publicname.
						'</div>'.
						'<div>'.
							'<a href="'.Url::_(array('ns' => 'network', 'ctl' => 'post'), array('id' => $id, 'ws' => $website)).'">'.
								Lang::_('View').
							'</a>'.
						'</div>'.
					'</div>'.
				 '</li>';
		
		}
	
	}

?>