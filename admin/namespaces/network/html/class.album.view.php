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
		* Views for album controller
		*
		* @package		Admin
		* @subpackage	Network\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Album extends Master{
	
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
			* Display an external album metadata
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	object	[$user]
			* @param	string	[$permalink]
			* @param	string	[$description]
			* @param	string	[$date]
			* @param	string	[$website]
			* @param	string	[$website_title]
		*/
		
		public static function album($id, $name, $user, $permalink, $description, $date, $website, $website_title){
		
			echo '<section id=network_album>'.
					'<h2>'.$name.'</h2>'.
					'<details>'.
						'<summary>'.
							Lang::_(
								'Published by %name the %date',
								'network',
								array(
									'name' => '<span>'.$user->_publicname.'</span>',
									'date' => date('d/m/Y @ H:i', strtotime($date))
								)
							).
						'</summary>'.
						'<p>'.nl2br($description).'</p>'.
					'</details>'.
				 '</section>';
		
		}
		
		/**
			* Display album pictures
			*
			* @static
			* @access	public
			* @param	array	[$pictures]
			* @param	string	[$website]
			* @param	string	[$website_title]
		*/
		
		public static function pictures($pictures, $website, $website_title){
		
			echo '<section id=network_album_pictures>';
					
					foreach($pictures as $p){
					
						$dir = dirname($p->_permalink).'/';
						$fname = basename($p->_permalink);
						
						echo '<figure>'.
								'<a class=fancybox rel=album href="'.$website.$p->_permalink.'" title="'.$p->_name.' | '.$p->_description.'">'.
									'<img src="'.$website.$dir.'300-'.$fname.'" alt="'.$p->_name.' | '.$p->_description.'" />'.
								'</a>'.
								'<figcaption>'.
									$p->_name.
								'</figcaption>'.
							 '</figure>';
					
					}
					
			echo '</section>';
		
		}
		
		/**
			* Display post comments
			*
			* @static
			* @access	public
			* @param	array	[$comments]
			* @param	integer	[$album_id]
			* @param	integer	[$website_id]
		*/
		
		public static function comments($comments, $album_id, $website_id){
		
			echo '<aside id=network_comments data-url="'.Url::_(array('ns' => 'network', 'ctl' => 'comments')).'" data-id="'.$album_id.'" data-ws="'.$website_id.'" data-type=media>'.
					'<div id=form>'.
						'<textarea class=txta name=content wrap=soft placeholder="'.Lang::_('Your comment', 'comments').'" required></textarea><br/>'.
						'<input class="button publish" type="submit" value="'.Lang::_('Reply').'" />'.
					'</div>'.
					'<ul>';
					
					foreach($comments as $c)
						echo '<li>'.
								'<details>'.
									'<summary>'.
										$c->_name.' ('.date('d/m/Y @ H:i', strtotime($c->_date)).')'.
									'</summary>'.
								'</details>'.
								'<p>'.
									nl2br($c->_content).
								'</p>'.
							 '</li>';
					
			echo	'</ul>'.
				 '</aside>';
		
		}
	
	}

?>