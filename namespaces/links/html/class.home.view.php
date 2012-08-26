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
	
	namespace Site\Links\Html;
	use \Site\Master\Html\Master;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for links home controller
		*
		* @package		Site
		* @subpackage	Links\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Home extends Master{
	
		/**
			* Display links list structure and a header to explicit the page
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function links($part){
		
			if($part == 'o')
				echo '<h1 class=page_title>Links</h1>'.
					 '<section id=links>';
			else
				echo '</section>';
		
		}
		
		/**
			* Display a link element
			*
			* @static
			* @access	public
			* @param	string	[$name]
			* @param	string	[$link]
			* @param	string	[$rss]
			* @param	string	[$notes]
		*/
		
		public static function link($name, $link, $rss, $notes){
		
			echo '<div class=llink>'.
					'<h3>'.$name.'</h3>'.
					'<div class=lldetails>'.
						'Website: <a href="'.$link.'" target=_blank>'.$link.'</a><br/>'.
						((!empty($rss))?'RSS: <a href="'.$rss.'" target=_blank>'.$rss.'</a>':'').
					'</div>'.
					'<p class=llnotes>'.nl2br($notes).'</p>'.
				 '</div>';
		
		}
		
		/**
			* Display a note about displayed links
			*
			* @static
			* @access	public
		*/
		
		public static function infos(){
		
			echo '<div id=links_infos>'.
					'A list of links you should take a look to.'.
				 '</div>';
		
		}
	
	}

?>