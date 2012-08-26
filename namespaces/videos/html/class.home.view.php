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
	
	namespace Site\Videos\Html;
	use \Site\Master\Html\Master;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for videos home controller
		*
		* @package		Site
		* @subpackage	Videos\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Home extends Master{
	
		/**
			* Display videos list structure and a header to explicit the page
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function videos($part){
		
			if($part == 'o')
				echo '<h1 class=page_title>Videos</h1>'.
					 '<section id=videos>';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display a video element
			*
			* @static
			* @access	public
			* @param	integer			[$id]
			* @param	string			[$name]
			* @paral	string			[$type]
			* @param	object			[$user]
			* @param	string			[$permalink]
			* @param	string			[$description]
			* @param	string			[$date]
			* @param	object			[$extra]
			* @param	object|boolean	[$fallback]
		*/
		
		public static function video($id, $name, $type, $user, $permalink, $description, $date, $extra, $fallback){
		
			echo '<figure>'.
					 '<div class=element>'.
						 '<video class=video src="'.WS_URL.$permalink.'" preload=metadata data-id="'.$id.'" data-name="'.$name.'" data-mime="'.$type.'" data-user="'.$user->_publicname.'">'.
							(($fallback !== false)
								?$fallback->_embed_code
								:'Video tag not supported but you can <a href="'.WS_URL.$permalink.'" target=_blank>download</a> the video.'
							).
						 '</video>'.
					 '</div>'.
					 ((!empty($description))
						?'<figcaption>'.
						 	'<p>'.nl2br($description).'</p>'.
						 '</figcaption>'
						:''
					 ).
				 '</figure>';
		
		}
		
		/**
			* Display videos categories list
			*
			* @static
			* @access	public
			* @param	array	[$cats] Array of categories objects
		*/
		
		public static function categories($cats){
		
			echo '<div id=videos_cats class=categories>'.
					'<h3>Categories</h3>'.
					'<ul>';
			
					foreach($cats as $c)
						echo '<li>'.
								'<a href="'.Url::_(array('ns' => 'videos', 'ctl' => 'category', 'id' => $c->_id)).'">'.
									$c->_name.
								'</a>'.
							 '</li>';
			
			echo 	'</ul>'.
				 '</div>';
		
		}
	
	}

?>