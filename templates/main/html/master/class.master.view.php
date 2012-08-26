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
	
	namespace Template\Main\Master;
	use \Library\Variable\Get as VGet;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Contains general views for the template
		*
		* @package		Template
		* @subpackage	Main\Master
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Master{
	
		/**
			* Display page menu
			*
			* @static
			* @access	public
			* @param	array	[$menu]
		*/
		
		public static function menu($menu){
		
			echo '<ul>';
					
					foreach($menu as $i){
					
						$link = array('ns' => $i->namespace);
						
						if(isset($i->controller))
							$link['ctl'] = $i->controller;
						
						if(isset($i->id))
							$link['id'] = $i->id;
						
						echo '<li '.((VGet::ns() == $i->namespace)?'class=selected':'').'>'.
								'<a href="'.Url::_($link).'">'.
									$i->text.
								'</a>'.
							 '</li>';
					
					}
					
			echo '</ul>';
		
		}
		
		/**
			* Display a pagination, for listings controllers
			*
			* @static
			* @access	public
			* @param	integer	[$p] Current page
			* @param	integer	[$max] Maximum pages available
			* @param	array	[$links] Additional GET parameter, if in a search
		*/
		
		public static function pagination($p, $max, array $links = array()){
		
			if(VGet::id(false))
				$firsts = array('ns' => VGet::ns('homepage'), 'ctl' => VGet::ctl('home'), 'id' => VGet::id());
			else
				$firsts = array('ns' => VGet::ns('homepage'), 'ctl' => VGet::ctl('home'));
			
			echo '<div id="pagination">';
			
			if($p < $max)
				echo '<a href="'.Url::_($firsts, $links).((empty($links))?'?':'&').'p='.($p+1).'">Previous</a>';
			
			if($p > 1)
				echo '<a class=next href="'.Url::_($firsts, $links).((empty($links))?'?':'&').'p='.($p-1).'">Next</a>';
			
			echo '</div>';
		
		}
		
		/**
			* Display a search box
			*
			* @static
			* @access	public
			* @param	string		[$searched] Element searched
			* @param	interger	[$count] Elements found by the search
		*/
		
		public static function search($searched = '', $count = false){
		
			echo '<div id=search_box>'.
					'<form method=get action="'.WS_URL.((URL_REWRITING)?'search':'').'" accept-charset="utf-8">'.
						((!URL_REWRITING)?'<input type=hidden name=ns value=search />':'').
						'<input id=search_input class=input type=search name="s" value="'.$searched.'" placeholder="Search" list=searchElements x-webkit-speech />'.
					'</form>'.
					(($count !== false)
						?'<div id=sbfound>'.$count.' element(s) found</div>'
						:''
					).
				 '</div>';
		
		}
		
		/**
			* Display comments list structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
			* @param	integer	[$id]
			* @param	string	[$type]
		*/
		
		public static function comments($part, $id = 0, $type = ''){
		
			if($part == 'o')
				echo '<section id=comments>'.
						'<form method=post>'.
							'<div id=cform data-url="'.WS_URL.'admin/?ns=api&ctl=comments" data-id="'.$id.'" data-type="'.$type.'">'.
								'<noscript><div class="message wrong">Javascript need no be activated</div></noscript>'.
								'<input class=input type=text name=name placeholder="Your name*" required />'.
								'<input class=input type=email name=email placeholder="E-mail*" required /><br/>'.
								'<textarea name=content placeholder="Feel free to comment" required></textarea><br/>'.
								'<button class=button>Submit</button>'.
							'</div>'.
						'</form>'.
						'<ul>';
			elseif($part == 'c')
				echo 	'</ul>'.
					 '</section>';
		
		}
		
		/**
			* Display a comment
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$email]
			* @param	string	[$content]
			* @param	string	[$date]
		*/
		
		public static function comment($id, $name, $email, $content, $date){
		
			echo '<li id=comment_'.$id.'>'.
					'<div class=cavatar>'.
						'<img src="http://www.gravatar.com/avatar/'.md5(strtolower($email)).'?s=80" alt="" />'.
					'</div>'.
					'<div class=ccontent>'.
						'<div class=ccinfos>'.
							'<span class=cciname>'.$name.'</span>'.
							'<span class=cciadd>('.date('d/m/Y @ H:i', strtotime($date)).') <a href="#comment_'.$id.'">permalink</a></span>'.
						'</div>'.
						'<p>'.nl2br($content).'</p>'.
					'</div>'.
				 '</li>';
		
		}
		
		/**
			* Display share buttons
			*
			* @static
			* @access	public
			* @param	array	[$allowed]
			* @param	string	[$url]
			* @param	string	[$title]
		*/
		
		public static function share($allowed, $url, $title){
		
			if(in_array('twitter', $allowed))
				echo '<a class=stwitter rel=nofollow href="http://twitter.com/share?url='.urlencode($url).'&amp;via=Lynxpressorg&amp;text='.urlencode($title).'" title=Tweet target="_blank">Tweet</a>';
			
			if(in_array('facebook', $allowed))
				echo '<a class=sfacebook rel=nofollow href="http://www.facebook.com/sharer.php?u='.urlencode($url).'&t='.urlencode($title).'" title="Share on facebook" target="_blank">Facebook</a>';
			
			if(in_array('google', $allowed))
				echo '<div class=sgoogle><div class="g-plusone"></div></div>';
		
		}
		
		/**
			* Display a message to say that there's nothing to display
			*
			* @static
			* @access	public
		*/
		
		public static function no_data(){
		
			echo '<div id=nodata>Oops! No data found!</div>';
		
		}
	
	}

?>