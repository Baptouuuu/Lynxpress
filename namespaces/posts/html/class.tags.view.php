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
	use \Library\Variable\Get as VGet;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for posts tags controller
		*
		* @package		Site
		* @subpackage	Posts\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Tags extends Home{
	
		/**
			* Display the name of the tag currently searched
			*
			* @static
			* @access	public
			* @param	string	[$tag]
		*/
		
		public static function tag($tag){
		
			echo '<div id=searched_tag>'.
					'<h3>Tag searched: "'.$tag.'"</h3>'.
				 '</div>';
		
		}
	
	}

?>