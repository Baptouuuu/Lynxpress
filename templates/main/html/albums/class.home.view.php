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
	
	namespace Template\Main\Albums;
	use \Template\Main\Master\Master;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for albums home controller
		*
		* @package		Template
		* @subpackage	Main\Albums
		* @author		Baptiste Langlade
		* @version		1.0
		* @abstract
	*/
	
	abstract class Home extends Master{
	
		/**
			* Display albums list structure and a header to explicit the page
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function albums($part){
		
			if($part == 'o')
				echo '<h1 class=page_title>Albums</h1>'.
					 '<section id=albums>';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display an album link
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	object	[$user]
			* @param	array	[$categories]
			* @param	string	[$permalink]
			* @param	string	[$date]
		*/
		
		public static function album($id, $name, $user, $categories, $permalink, $date){
		
			echo '<figure class=album>'.
					'<a href="'.Url::_(array('ns' => 'albums', 'ctl' => 'view', 'id' => $id)).'">'.
						'<img src="'.WS_URL.$permalink.'cover.png" alt="cover" />'.
					'</a>'.
					'<figcaption>'.
						'<a href="'.Url::_(array('ns' => 'albums', 'ctl' => 'view', 'id' => $id)).'">'.
							'<p>'.$name.'</p>'.
						'</a>'.
					'</figcaption>'.
				 '</figure>';
		
		}
		
		/**
			* Display albums categories list
			*
			* @static
			* @access	public
			* @param	array	[$cats] Array of categories objects
		*/
		
		public static function categories($cats){
		
			echo '<div id=albums_cats class=categories>'.
					'<h3>Categories</h3>'.
					'<ul>';
			
					foreach($cats as $c)
						echo '<li>'.
								'<a href="'.Url::_(array('ns' => 'albums', 'ctl' => 'category', 'id' => $c->_id)).'">'.
									$c->_name.
								'</a>'.
							 '</li>';
			
			echo 	'</ul>'.
				 '</div>';
		
		}
	
	}

?>