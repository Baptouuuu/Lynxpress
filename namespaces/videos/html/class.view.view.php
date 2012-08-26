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
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for videos view controller
		*
		* @package		Site
		* @subpackage	Videos\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class View extends Home{
	
		/**
			* Display videos list structure and a header to explicit the page
			*
			* @static
			* @access	public
			* @param	string	[$part]
			* @param	string	[$name]
		*/
		
		public static function videos($part, $name = ''){
		
			if($part == 'o')
				echo '<h1 class=page_title>'.$name.'</h1>'.
					 '<section id=videos>';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display some informations about the author
			*
			* @static
			* @access	public
			* @param	string	[$publicname]
			* @param	string	[$email]
			* @param	string	[$website]
			* @param	string	[$msn]
			* @param	string	[$twitter]
			* @param	string	[$facebook]
			* @param	string	[$google]
			* @param	string	[$bio]
		*/
		
		public static function author($publicname, $email, $website, $msn, $twitter, $facebook, $google, $bio){
		
			echo '<div id=about_author>'.
					'<h3>About the author</h3>'.
					'<div id=aaavatar>'.
						'<img src="http://www.gravatar.com/avatar/'.md5(strtolower($email)).'?s=80" alt="" />'.
					'</div>'.
					'<p id=aabio>'.nl2br($bio).'</p>'.
					'<div id=aacontact>'.
						((!empty($twitter))?'<a href="'.$twitter.'" target=_blank>Twitter</a> ':'').
						((!empty($facebook))?'<a href="'.$facebook.'" target=_blank>Facebook</a> ':'').
						((!empty($google))?'<a href="'.$google.'" target=_blank>Google+</a> ':'').
					'</div>'.
				 '</div>';
		
		}
	
	}

?>