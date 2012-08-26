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
	
	namespace Template\Main\Posts;
	use \Template\Main\Master\Master;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for posts home controller
		*
		* @package		Template
		* @subpackage	Main\Posts
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class View extends Master{
	
		/**
			* Display an article
			*
			* @static
			* @access	public
			* @param	string	[$title]
			* @param	string	[$content]
			* @param	string	[$date]
			* @param	object	[$user]
			* @param	array	[$categories]
			* @param	array	[$tags]
			* @param	object	[$extra]
			* @param	array	[$share]
			* @param	string	[$permalink]
		*/
		
		public static function post($title, $content, $date, $user, $categories, $tags, $extra, $share, $permalink){
		
			if(!empty($extra->banner)){
			
				$bdir = dirname($extra->banner->_permalink).'/';
				$bname = basename($extra->banner->_permalink);
			
			}
			
			foreach($categories as &$c)
				$c = '<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'category', 'id' => $c->_id)).'">'.$c->_name.'</a>';
			
			if(is_array($tags))
				foreach($tags as &$t)
					$t = '<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'tags', 'id' => trim($t))).'">'.trim($t).'</a>';
			else
				$tags = array('<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'tags', 'id' => trim($tags))).'">'.trim($tags).'</a>');
			
			echo '<article id=post>'.
					((!empty($extra->banner))?
						'<div id=pbanner>'.
							'<img src="'.WS_URL.$bdir.'1000-'.$bname.'" alt="'.$extra->banner->_name.'" />'.
						'</div>'
					:'').
					'<h1><span>'.$title.'</span></h1>'.
					'<div id=pinfos>'.
						'Published the <time datetime="'.date(DATE_ATOM, strtotime($date)).'" pubdate>'.date('F d, Y', strtotime($date)).'</time>'.
						' | Categories: '.implode(', ', $categories).
					'</div>'.
					'<div id=pcontent>'.nl2br($content).'</div>'.
					'<div id=ptags>'.
						'Tags: '.implode(', ', $tags).
					'</div>';
					
					if(!empty($extra->gallery)){
					
						echo '<section id=pgallery>';
						
							foreach($extra->gallery->pics as $p){
							
								$pdir = dirname($p->_permalink).'/';
								$pname = basename($p->_permalink);
								
								echo '<div>'.
										'<a href="'.WS_URL.$p->_permalink.'" data-rel=lightbox rel=gallery title="'.$p->_description.'">'.
											'<img src="'.WS_URL.$pdir.'150-'.$pname.'" alt="'.$p->_name.'" />'.
										'</a>'.
									 '</div>';
							
							}
						
						echo '</section>';
					
					}
					
			echo '</article>'.
				 '<div class=share>';
				 
				 parent::share($share, Url::_(array('ns' => 'posts', 'ctl' => 'view', 'id' => $permalink)), $title);
			
			echo '</div>';
		
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
		
		/**
			* Display a list of related posts
			*
			* @static
			* @access	public
			* @param	array	[$posts] Array of post objects, only _title and _permalink available
		*/
		
		public static function related($posts){
		
			if(!empty($posts)){
			
				echo '<div id=related_posts>'.
						'<h3>Related Posts</h3>'.
						'<ul>';
				
							foreach($posts as $p)
								echo '<li>'.
										'<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'view', 'id' => $p->_permalink)).'">'.
											$p->_title.
										'</a>'.
									 '</li>';
				
				echo 	'</ul>'.
					 '</div>';
			
			}
		
		}
	
	}

?>