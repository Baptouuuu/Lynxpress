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
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Network\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
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
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'network')).'">'.Lang::_('Network').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'network', 'ctl' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display a message to tell the user what he's seeing
			*
			* @static
			* @access	public
		*/
		
		public static function head(){
		
			echo '<h2>'.Lang::_('Activity on your network since your last visit', 'network').'</h2>';
		
		}
		
		/**
			* Display the structure of websites list
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function websites($part){
		
			if($part == 'o')
				echo '<ul id=network_list>';
			elseif($part == 'c')
				echo '</ul>';
		
		}
		
		/**
			* Display a website in the list
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$url]
			* @param	integer	[$posts]
			* @param	integer	[$albums]
			* @param	integer	[$videos]
		*/
		
		public static function website($id, $name, $url, $posts, $albums, $videos){
		
			echo '<li>'.
					'<div class=name>'.
						$name.
					'</div>'.
					'<div class=links>'.
						'<div>'.
							'<a href="'.Url::_(array('ns' => 'network', 'ctl' => 'posts'), array('ws' => $id)).'">'.
								Lang::_('Posts').
							'</a>'.
							'<span class=count>'.
								$posts.
							'</span>'.
						'</div>'.
						'<div>'.
							'<a href="'.Url::_(array('ns' => 'network', 'ctl' => 'albums'), array('ws' => $id)).'">'.
								Lang::_('Album').
							'</a>'.
							'<span class=count>'.
								$albums.
							'</span>'.
						'</div>'.
						'<div>'.
							'<a href="'.Url::_(array('ns' => 'network', 'ctl' => 'videos'), array('ws' => $id)).'">'.
								Lang::_('Video').
							'</a>'.
							'<span class=count>'.
								$videos.
							'</span>'.
						'</div>'.
						'<div>'.
							'<a href="'.$url.'" target=_blank>'.
								Lang::_('View').
							'</a>'.
						'</div>'.
					'</div>'.
				 '</li>';
		
		}
	
	}

?>