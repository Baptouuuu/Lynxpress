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
	
	namespace Site\Posts\Html;
	use \Site\Master\Html\Master;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for posts home controller
		*
		* @package		Site
		* @subpackage	Posts\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Home extends Master{
	
		/**
			* Display posts list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function posts($part){
		
			if($part == 'o')
				echo '<section id=posts>';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display an article preview
			*
			* @static
			* @access	public
			* @param	string		[$title]
			* @param	string		[$content]
			* @param	string		[$date]
			* @param	object		[$user]
			* @param	array		[$category]
			* @param	string		[$permalink]
			* @param	null|object	[$banner] 'null' if post doesn't have a banner, otherwise Media object of the banner
		*/
		
		public static function post($title, $content, $date, $user, $category, $permalink, $banner){
		
			foreach($category as &$c)
				$c = '<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'category', 'id' => $c->_id)).'">'.$c->_name.'</a>';
			
			if(!empty($banner)){
			
				$bdir = dirname($banner->_permalink).'/';
				$bname = basename($banner->_permalink);
			
			}
			
			echo '<article class=article>'.
					((!empty($banner))?
						'<div class=banner>'.
							'<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'view', 'id' => $permalink)).'">'.
								'<img src="'.WS_URL.$bdir.'1000-'.$bname.'" alt="'.$banner->_name.'" />'.
							'</a>'.
						'</div>'
					:'').
					'<h2>'.
						'<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'view', 'id' => $permalink)).'">'.
							$title.
						'</a>'.
					'</h2>'.
					'<div class=infos>'.
						'By '.$user->_publicname.' the <time datetime="'.date(DATE_ATOM, strtotime($date)).'" pubdate>'.date('d/m/Y', strtotime($date)).'</time> | Categories: '.implode(', ', $category).
					'</div>'.
					'<p class=content>'.nl2br($content).'...</p>'.
				 '</article>';
		
		}
		
		/**
			* Display posts categories list
			*
			* @static
			* @access	public
			* @param	array	[$cats] Array of categories objects
		*/
		
		public static function categories($cats){
		
			echo '<div id=posts_cats class=categories>'.
					'<h3>Categories</h3>'.
					'<ul>';
			
					foreach($cats as $c)
						echo '<li>'.
								'<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'category', 'id' => $c->_id)).'">'.
									$c->_name.
								'</a>'.
							 '</li>';
			
			echo 	'</ul>'.
				 '</div>';
		
		}
	
	}

?>