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
		* Views for post controller
		*
		* @package		Admin
		* @subpackage	Network\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Post extends Master{
	
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
			* Display an external post
			*
			* @static
			* @access	public
			* @param	string	[$title]
			* @param	string	[$content]
			* @param	string	[$date]
			* @param	object	[$user]
			* @param	string	[$tags]
			* @param	string	[$permalink]
			* @param	string	[$website]
			* @param	string	[$website_title]
		*/
		
		public static function post($title, $content, $date, $user, $tags, $permalink, $website, $website_title){
		
			echo '<section id=network_post>'.
					'<h2>'.$title.'</h2>'.
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
					'</details>'.
					'<article>'.
						nl2br($content).
					'</article>'.
					'<a class=button href="'.$website.'?ns=posts&id='.$permalink.'" target=_blank>'.
						Lang::_('View this post on %ws', 'network', array('ws' => $website_title)).
					'</a>'.
				 '</section>';
		
		}
		
		/**
			* Display post comments
			*
			* @static
			* @access	public
			* @param	array	[$comments]
			* @param	integer	[$post_id]
			* @param	integer	[$website_id]
		*/
		
		public static function comments($comments, $post_id, $website_id){
		
			echo '<aside id=network_comments data-url="'.Url::_(array('ns' => 'network', 'ctl' => 'comments')).'" data-id="'.$post_id.'" data-ws="'.$website_id.'" data-type=post>'.
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