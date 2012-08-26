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
	
	namespace Site\Albums\Html;
	use \Site\Master\Html\Master;
	use \Library\Url\Url;
	use \Library\Variable\Get as VGet;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for albums view controller
		*
		* @package		Site
		* @subpackage	Albums\Html
		* @author		Baptiste Langlade
		* @version		1.0
		* @abstract
	*/
	
	abstract class View extends Master{
	
		/**
			* Display album informations
			*
			* @static
			* @access	public
			* @param	string	[$part]
			* @param	string	[$name]
			* @param	string	[$date]
			* @param	array	[$categories]
			* @param	string	[$description]
			* @param	object	[$extra]
		*/
		
		public static function album($name, $date, $categories, $description, $extra, $share, $id){
		
			foreach($categories as &$c)
				$c = '<a href="'.Url::_(array('ns' => 'albums', 'ctl' => 'category', 'id' => $c->_id)).'">'.$c->_name.'</a>';
			
			echo '<h1 class=page_title>'.$name.'</h1>'.
				 '<section id=album>'.
				 	'<div id=adetails>'.
				 		'Published the <time datetime="'.date(DATE_ATOM, strtotime($date)).'" pubdate>'.date('F d, Y', strtotime($date)).'</time>'.
				 		' | Categories: '.implode(', ', $categories).
				 	'</div>'.
				 	'<p id=adescription>'.nl2br($description).'</p>'.
				 '</section>'.
				 '<div class=share>';
			
				parent::share($share, Url::_(array('ns' => 'albums', 'ctl' => 'view', 'id' => $id)), $name);
			
			echo '</div>';
		
		}
		
		/**
			* Display pictures list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function pictures($part){
		
			if($part == 'o')
				echo '<section id=album_pictures>';
			elseif($part == 'c')
				echo '</section>';
		
		}
		
		/**
			* Display a picture of the album
			*
			* @static
			* @access	public
			* @param	string	[$name]
			* @param	string	[$permalink]
			* @param	string	[$description]
			* @param	string	[$date]
			* @param	object	[$extra]
		*/
		
		public static function picture($name, $permalink, $description, $date, $extra){
		
			$pdir = dirname($permalink).'/';
			$pname = basename($permalink);
			
			echo '<figure class=appicture>'.
					'<a href="'.WS_URL.$permalink.'" data-rel=lightbox rel=gallery title="'.$description.'">'.
						'<img src="'.WS_URL.$pdir.'150-'.$pname.'" alt="'.$name.'" />'.
					'</a>'.
				 '</figure>';
		
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