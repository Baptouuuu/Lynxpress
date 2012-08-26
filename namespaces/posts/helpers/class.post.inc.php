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
	
	namespace Site\Posts\Helpers;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Helper to make operations on a post (such as making a preview of a post)
		*
		* @package		Site
		* @subpackage	Posts\Helpers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Post{
	
		/**
			* Make a preview of an article by reducing the text lenght
			* But making sure its not croping a tag in half
			*
			* @static
			* @access	public
			* @param	string	[$post] Post content
			* @return	string
		*/
		
		public static function trim($post){
		
			$tags = array('<a ' => '</a>', '<ul' => '</ul>', '<img ' => '/>', '<strong>' => '</strong>', '<em>' => '</em>', '<blockquote>' => '</blockquote>', '<code' => '</code>');
			
			//check in a tag exist in the post
			foreach($tags as $start_tag => $end_tag)
				if(stripos($post, $start_tag) !== false)
					$pos_start_tags[$start_tag] = stripos($post, $start_tag);
			
			//if there's a tag in the post, we retrieve the first one
			if(isset($pos_start_tags)){
				
				$first_tag_position = min($pos_start_tags);
			
				foreach($pos_start_tags as $start_tag => $position)
					if($position == $first_tag_position)
						$tag_to_find = $start_tag;
			
			}
			
			//if no tag found we crop at 500 characters
			if(!isset($tag_to_find))
				$crop_length = 500;
			else
				$crop_length = (stripos($post, $tags[$tag_to_find]) + strlen($tags[$tag_to_find]));
			
			return substr($post, 0, $crop_length);
		
		}
	
	}

?>