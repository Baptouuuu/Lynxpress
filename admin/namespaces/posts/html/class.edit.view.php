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
	
	namespace Admin\Posts\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Posts\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Edit extends Master{
	
		/**
			* Display the menu of posts edit page
			*
			* @static
			* @access	public
			* @param	boolean [$edit]
		*/
		
		public static function menu($edit = false){
		
			if($edit === false){
			
				echo '<div id="menu">'.
						'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'edit')).'">'.Lang::_('Add').'</a></span>'.
					 	'<span class="menu_item"><a href="'.Url::_(array('ns' => 'posts')).'">'.Lang::_('Posts').'</a></span>'.
					 '</div>';
			
			}elseif($edit === true){
			
				echo '<div id="menu">'.
						'<span class="menu_item"><a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'edit')).'">'.Lang::_('Add').'</a></span>'.
						'<span id="menu_selected" class="menu_item"><a href="#">'.Lang::_('Edition').'</a></span>'.
					 	'<span class="menu_item"><a href="'.Url::_(array('ns' => 'posts')).'">'.Lang::_('Posts').'</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display actions structure
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function actions($part){
		
			if($part == 'o'){
			
				echo '<div id="actions">';
			
			}elseif($part == 'c'){
			
				echo '</div>';
			
			}
		
		}
		
		/**
			* Display a button to save post as draft
			*
			* @static
			* @access	public
		*/
		
		public static function save_draft(){
		
			echo '<input class="button" type="submit" name="save_draft" value="'.Lang::_('Save as Draft').'" /> ';
		
		}
		
		/**
			* Display a button to publish the post
			*
			* @static
			* @access	public
		*/
		
		public static function publish(){
		
			echo '<input class="button publish" type="submit" name="publish" value="'.Lang::_('Publish').'" />';
		
		}
		
		/**
			* Display a link to preview the post
			*
			* @static
			* @access	public
			* @param	string [$permalink]
		*/
		
		public static function preview($permalink){
		
			echo '<a class="button" href="'.Url::_(array('ns' => 'posts', 'ctl' => 'view', 'id' => $permalink), array(), true).'" target="_blank">'.Lang::_('Preview').'</a> ';
		
		}
		
		/**
			* Display a link to view the post
			*
			* @static
			* @access	public
			* @param	string [$permalink]
		*/
		
		public static function view($permalink){
		
			echo '<a class="button" href="'.Url::_(array('ns' => 'posts', 'ctl' => 'view', 'id' => $permalink), array(), true).'" target="_blank">'.Lang::_('View').'</a> ';
		
		}
		
		/**
			* Display a button to update the post
			*
			* @static
			* @access	public
		*/
		
		public static function update(){
		
			echo '<input class="button publish" type="submit" name="update" value="'.Lang::_('Update').'" />';
		
		}
		
		/**
			* Display post structure
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function post_wrapper($part){
		
			if($part == 'o'){
			
				echo '<section id="post_form">';
			
			}elseif($part == 'c'){
			
				echo '</section>';
			
			}
		
		}
		
		/**
			* Display informations about the post
			*
			* @static
			* @access	public
			* @param	int [$id]
			* @param	string [$allow_comment]
			* @param	string [$date]
			* @param	object [$user]
			* @param	string [$action]
		*/
		
		public static function infos($id, $allow_comment, $date, $user, $action){
		
			echo '<details open>'.
					Lang::_('Created by').': '.$user->_username.' '.
					Lang::_('the').' '.date('d/m/Y @ H:i', strtotime($date)).' | '.
					'<span id="comment">'.
						'<input id="allow_comment" type="checkbox" name="allow_comment" value="open" '.(($allow_comment == 'open')?'checked':'').' />'.
						'<label for="allow_comment">'.Lang::_('Allow Comments').'</label>'.
					'</span>'.
					'<input id=post_id type="hidden" name="id" value="'.$id.'" />'.
					'<input type="hidden" name="action" value="'.$action.'" />'.
				 '</details>';
		
		}
		
		/**
			* Display post form
			*
			* @static
			* @access	public
			* @param	integer [$id]
			* @param	string [$title]
			* @param	string [$content]
			* @param	string [$permalink]
			* @param	boolean [$display_permalink]
			* @param	string [$status]
			* @param	array [$pictures]
			* @param	array [$videos]
		*/
		
		public static function post($id, $title, $content, $permalink, $display_permalink, $status){
		
			echo '<input id="pf_title" class=input type="text" name="title" value="'.$title.'" placeholder="'.Lang::_('Title').'" required x-webkit-speech /><br/>';
			
			if($display_permalink === true)
				echo 'Permalink: <a class="button" href="'.Url::_(array('ns' => 'posts', 'ctl' => 'view', 'id' => $permalink), (($status == 'draft')?array('preview' => 'true'):array()), true).'">'.Url::_(array('ns' => 'posts', 'ctl' => 'view', 'id' => $permalink), array(), true).'</a>';
			
			echo '<div class="txta_actions" data-id="pf_content">'.
					'<button class="button" data-form="add_link_form">'.Lang::_('Add Link').'</button>'.
					'<button class="button" data-form="add_title_form">'.Lang::_('Add Title').'</button>'.
					'<button class="button" data-form="add_image_form">'.Lang::_('Add Image').'</button>'.
					'<button class="button" data-form="add_video_form">'.Lang::_('Add Video').'</button>'.
				 '</div>';
			
			echo '<textarea id="pf_content" class=txta name="content" placeholder="'.Lang::_('What do you want to share today?', 'posts').'" required>'.$content.'</textarea>'.
				 '<fieldset>'.
				 	'<legend>'.Lang::_('Categories').'</legend>';
		
		}
		
		/**
			* Display categories fieldset end tag and post tags
			*
			* @static
			* @access	public
			* @param	string [$tags]
		*/
		
		public static function tags($tags){
		
			echo '</fieldset>'.
				 '<input id="pf_tags" class="input" type="text" name="tags" value="'.$tags.'" placeholder="'.Lang::_('Tags, separetad with commas', 'posts').'" /><br/>';
		
		}
		
		/**
			* Display a category element
			*
			* @static
			* @access	public
			* @param	int [$id]
			* @param	string [$name]
			* @param	boolean [$checked]
		*/
		
		public static function category($id, $name, $checked){
		
			echo '<span class="acat"><input id="cat_'.$id.'" type="checkbox" name="category[]" value="'.$id.'" '.(($checked === true)?'checked':'').'><label for="cat_'.$id.'">'.$name.'</label></span>';
		
		}

		/**
			* Display a form to add a banner and a galery to the post
			*
			* @static
			* @access	public
		*/

		public static function extra(){
			
			echo '<fieldset id="post_extra">'.
					'<legend>'.Lang::_('Extra').'</legend>'.
					'<div id="post_extra_buttons">'.
						'<button class="button modify" data-form="pbi_form">'.Lang::_('Modify Banner').'</button> '.
						'<button class="button modify" data-form="pga_form">'.Lang::_('Modify Gallery').'</button>'.
					'</div>'.
				 '</fieldset>';

		}
 		
		/**
			* Add popups to add content to the textarea and manipulate post extra informations
			*
			* @static
			* @access	public
			* @param	integer	[$gallery]
			* @param	integer	[$banner]
		*/
		
		public static function popups($gallery, $banner){
		
			echo '<section id=popups>'.
				 	'<div class="background"></div>'.
				 	'<div id="add_link_form" class="popup">'.
				 		'<div class="header">'.
				 			'<span>'.Lang::_('Add Link').'</span>'.
				 			'<button class="button cancel">X</button>'.
				 		'</div>'.
				 		'<div class="content">'.
				 			'<input id="ad_name" class="input" type="text" placeholder="'.Lang::_('Title').'" />'.
				 			'<input id="ad_url" class="input" type="url" placeholder="http://" /><br/>'.
				 			'<button class="button add">'.Lang::_('Add').'</button>'.
				 		'</div>'.
				 	'</div>'.
				 	'<div id="add_title_form" class="popup">'.
				 		'<div class="header">'.
				 			'<span>'.Lang::_('Add Title').'</span>'.
				 			'<button class="button cancel">X</button>'.
				 		'</div>'.
				 		'<div class="content">'.
				 			'<input id="at_title" class="input" type="text" placeholder="'.Lang::_('Title').'" /><br/>'.
				 			'<button class="button add">'.Lang::_('Add').'</button>'.
				 		'</div>'.
				 	'</div>'.
				 	'<div id="add_image_form" class="popup" data-lang="'.Lang::get_lang().'">'.
				 		'<div class="header">'.
				 			'<span>'.Lang::_('Add Image').'</span>'.
				 			'<button class="button cancel">X</button>'.
				 		'</div>'.
				 		'<div class="content">'.
				 			'<ul>'.
				 			'</ul>'.
				 		'</div>'.
				 	'</div>'.
				 	'<div id="add_video_form" class="popup" data-lang="'.Lang::get_lang().'">'.
		 		 		'<div class="header">'.
		 		 			'<span>'.Lang::_('Add Video').'</span>'.
		 		 			'<button class="button cancel">X</button>'.
		 		 		'</div>'.
		 		 		'<div class="content">'.
		 		 			'<ul>'.
		 		 			'</ul>'.
		 		 		'</div>'.
		 		 	'</div>'.
		 		 	'<div id="pbi_form" class="popup">'.
		 		 		'<div class="header">'.
		 		 			'<span>'.Lang::_('Add an image as banner').'</span>'.
		 		 			'<button class="button cancel">X</button>'.
		 		 		'</div>'.
		 		 		'<div class="content">'.
		 		 			'<ul>'.
		 		 				'<li>'.
		 		 					'<input id="pbi_image_none" type="radio" name="banner" value="" '.((empty($banner))?'checked':'').' />'.
		 		 					'<div class="pbi_image">'.
		 		 						'<label for="pbi_image_none">'.
		 		 							Lang::_('None').
		 		 						'</label>'.
		 		 					'</div>'.
		 		 				'</li>'.
		 		 			'</ul>'.
		 		 		'</div>'.
		 		 	'</div>'.
		 		 	'<div id="pga_form" class="popup">'.
		 		 		'<div class="header">'.
		 		 			'<span>'.Lang::_('Add an album as gallery').'</span>'.
		 		 			'<button class="button cancel">X</button>'.
		 		 		'</div>'.
		 		 		'<div class="content">'.
		 		 			'<ul>'.
		 		 				'<li class="button">'.
		 		 					'<label for="album_none">'.
		 		 						'<div class="check_label">'.
		 		 							'<input id="album_none" type="radio" name="gallery" value="" '.((empty($gallery))?'checked':'').' />'.
		 		 						'</div>'.
		 		 					'</label>'.
		 		 					'<div class="name">'.
		 		 						Lang::_('None').
		 		 					'</div>'.
		 		 				'</li>'.
		 		 			'</ul>'.
		 		 		'</div>'.
		 		 	'</div>'.
				 '</section>';
		
		}
		
		/**
			* Display datalists for pictures and videos to be used with javascript to build popups contents
			*
			* @static
			* @access	public
			* @param	array	[$pictures]
			* @param	array	[$videos]
			* @param	array	[$albums]
			* @param	integer	[$banner]
			* @param	integer	[$gallery]
		*/
		
		public static function media_datalists($pictures, $videos, $albums, $banner, $gallery){
		
			echo '<datalist id=pictures data-banner="'.$banner.'">';
			
					if(!empty($pictures))
						foreach($pictures as $p){
						
							$dir = dirname($p->_permalink).'/';
							$fname = basename($p->_permalink);
							
							echo '<option data-id="'.$p->_id.'" data-name="'.$p->_name.'" data-description="'.$p->_description.'" data-permalink="'.$p->_permalink.'" data-permalink-150="'.$dir.'150-'.$fname.'" data-permalink-300="'.$dir.'300-'.$fname.'" data-permalink-1000="'.$dir.'1000-'.$fname.'">';
						
						}
			
			echo '</datalist>'.
				 '<datalist id=videos>';
			
					if(!empty($videos))
						foreach($videos as $v)
							echo '<option data-id="'.$v->_id.'" data-name="'.$v->_name.'" data-permalink="'.$v->_permalink.'" data-fallback="'.((!empty($v->_fallback))?htmlspecialchars(htmlspecialchars($v->_fallback->_embed_code)):'').'">';
			
			echo '</datalist>'.
				 '<datalist id=albums data-gallery="'.$gallery.'">';
				 
				 	if(!empty($albums))
				 		foreach($albums as $a)
				 			echo '<option data-id="'.$a->_id.'" data-name="'.$a->_name.'" data-permalink="'.$a->_permalink.'">';
			
			echo '</datalist>';
		
		}
		
		/**
			* Display media templates to fill popups lists
			*
			* @static
			* @access	public
		*/
		
		public static function media_templates(){
		
			echo '<script id=tpl_picture type="media/template">'.
					'<li class="button">'.
					 	'<div class="thumb">'.
					 		'<a class="fancybox" href="'.WS_URL.'{{permalink}}">'.
					 			'<img src="'.WS_URL.'{{permalink-150}}" alt="{{description}}" />'.
					 		'</a>'.
					 	'</div>'.
					 	'<div class="name">'.
					 		'{{name}}'.
					 	'</div>'.
					 	'<div class="edit">'.
					 		'<a href="'.Url::_(array('ns' => 'media', 'ctl' => 'edit'), array('id' => '{{id}}')).'" target="_blank">'.Lang::_('Edit').'</a>'.
					 	'</div>'.
					 	'<div class="add">'.
					 		'<a class="add_button" data-link="'.WS_URL.'{{permalink-300}}" data-full="'.WS_URL.'{{permalink}}" data-description="{{description}}" data-name="{{name}}" href="#">'.Lang::_('Add').'</a>'.
					 	'</div>'.
					 '</li>'.
				 '</script>'.
				 '<script id=tpl_video type="media/template">'.
				 	'<li class="button">'.
			 		 	'<div class="name">'.
			 		 		'{{name}}'.
			 		 	'</div>'.
			 		 	'<div class="edit">'.
			 		 		'<a href="'.Url::_(array('ns' => 'media', 'ctl' => 'edit'), array('id' => '{{id}}')).'" target="_blank">'.
			 		 			Lang::_('Edit').
			 		 		'</a>'.
			 		 	'</div>'.
			 		 	'<div class="add">'.
			 		 		'<a class="add_button" data-link="'.WS_URL.'{{permalink}}" data-fallback="{{fallback}}" href="#">'.Lang::_('Add').'</a>'.
			 		 	'</div>'.
			 		 '</li>'.
				 '</script>'.
				 '<script id=tpl_banner type="media/template">'.
				 	'<li>'.
			 			'<input id="pbi_image_{{id}}" type="radio" name="banner" value="{{id}}" {{checked}} />'.
			 		 	'<div class="pbi_image">'.
			 		 		'<label for="pbi_image_{{id}}">'.
			 		 			'<img src="'.WS_URL.'{{permalink-1000}}" alt="{{name}}" />'.
			 		 		'</label>'.
			 		 	'</div>'.
			 		 	'<div class="pbi_meta">'.
			 		 		'<a class="fancybox" href="'.WS_URL.'{{permalink}}">'.
			 		 			Lang::_('View').
			 		 		'</a> | '.
			 		 		'{{name}}'.
			 		 	'</div>'.
			 		 '</li>'.
				 '</script>'.
				 '<script id=tpl_gallery type="media/template">'.
				 	'<li class="button">'.
			 		 	'<label for="album_{{id}}">'.
			 		 		'<div class="check_label">'.
			 		 			'<input id="album_{{id}}" type="radio" name="gallery" value="{{id}}" {{checked}} />'.
			 		 		'</div>'.
			 		 	'</label>'.
			 		 	'<div class="thumb">'.
			 		 		'<img src="'.WS_URL.'{{permalink}}150-cover.png" alt="cover" />'.
			 		 	'</div>'.
			 		 	'<div class="name">'.
			 		 		'{{name}}'.
			 		 	'</div>'.
			 		 '</li>'.
				 '</script>';
		
		}
	
	}

?>