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
	
	namespace Admin\Dashboard\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Html manage class
		*
		* Contains views for Manage controller for dashboard
		*
		* @package		Admin
		* @subpackage	Dashboard\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Manage extends Master{
	
		/**
			* Display the menu of dashboard page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
				 	'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'dashboard')).'">'.Lang::_('Dashboard').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display dashboard structure
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function dashboard($part){
		
			if($part == 'o'){
			
				echo '<div id="dashboard">'.
						'<div id="widgets_left">';
			
			}elseif($part == 'm'){
			
				echo 	'</div>'.
						'<div id="widgets_right">';
			
			}elseif($part == 'c'){
			
				echo 	'</div>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display acitivity widget structure
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function widget_activity($part){
		
			if($part == 'o'){
			
				echo '<div id="dash_activity" class="widget">'.
						'<div class="header">'.
							'<span>'.Lang::_('Activity').'</span>'.
							'<a class=toggle data-widget=dash_activity>'.
								'<span class=bar></span>'.
							'</a>'.
						'</div>'.
						'<div class="list">'.
							'<ul>';
			
			}elseif($part == 'c'){
			
				echo 		'</ul>'.
						'</div>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display an element in activity list
			*
			* @static
			* @access	public
			* @param	string [$username]
			* @param	string [$message]
			* @param	string [$date]
		*/
		
		public static function activity($username, $message, $date){
		
			echo '<li>'.$username.' '.$message.' ('.date('d/m/Y @ H:i', strtotime($date)).')</li>';
		
		}
		
		/**
			* Message displayed if no activity to display
			*
			* @static
			* @access	public
		*/
		
		public static function no_activity(){
		
			echo '<li>'.Lang::_('No acitivty', 'dashboard').'</li>';
		
		}
		
		/**
			* Display comments widget structure
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function widget_comments($part){
		
			if($part == 'o'){
			
				echo '<div id="dash_comments" class="widget">'.
						'<div class="header">'.
							'<span>'.Lang::_('Comments').'</span>'.
							'<a class=toggle data-widget=dash_comments>'.
								'<span class=bar></span>'.
							'</a>'.
							'<a class="button" href="'.Url::_(array('ns' => 'comments')).'">'.Lang::_('View All', 'dashboard').'</a>'.
						'</div>'.
						'<div class="list">'.
							'<ul>';
			
			}elseif($part == 'c'){
			
				echo 		'</ul>'.
						'</div>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display an element in comments list
			*
			* @static
			* @access	public
			* @param	int [$id]
			* @param	string [$name]
			* @param	string [$content]
			* @param	object [$rel] Relative element
		*/
		
		public static function comment($id, $name, $content, $rel){
		
			echo '<li>'.
					'<h4>From '.$name.' on <a href="'.$rel->_link.'">'.$rel->_rel_name.'</a></h4>'.
					'<p>'.htmlspecialchars($content).'</p>'.
					'<div class="row_actions">'.
						'<a class="green" href="'.Url::_(array('ns' => 'comments'), array('action' => 'update', 'to' => 'approved', 'id' => $id)).'">'.Lang::_('Approve').'</a> | '.
						'<a class="red" href="'.Url::_(array('ns' => 'comments'), array('action' => 'update', 'to' => 'spam', 'id' => $id)).'">'.Lang::_('Spam').'</a> | '.
						'<a class="red" href="'.Url::_(array('ns' => 'comments'), array('action' => 'update', 'to' => 'trash', 'id' => $id)).'">'.Lang::_('Trash').'</a>'.
					'</p>'.
				 '</li>';
		
		}
		
		/**
			* Message displayed if no pending comments
			*
			* @static
			* @access	public
		*/
		
		public static function no_comment(){
		
			echo '<li>'.Lang::_('No pending comments', 'dashboard').'</li>';
		
		}
		
		/**
			* Display new post widget
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function widget_post($part){
		
			if($part == 'o'){
			
				echo '<div id="dash_post" class="widget">'.
						'<div class="header">'.
							'<span>'.Lang::_('New Post', 'dashboard').'</span>'.
							'<a class=toggle data-widget=dash_post>'.
								'<span class=bar></span>'.
							'</a>'.
						'</div>'.
						'<div class="list">';
						
						parent::form('o', 'post', Url::_(array('ns' => 'posts', 'ctl' => 'edit')));
						
						echo '<div id="dp_inputs">'.
							 	'<input id="dp_title" class="input" type="text" name="title" placeholder="'.Lang::_('Title', 'dashboard').'" required /><br/>'.
							 	'<textarea id="dp_content" class="txta" name="content" placeholder="'.Lang::_('What do you want to share today?', 'posts').'" required></textarea><br/>'.
							 	'<input id="dp_tags" class="input" type="text" name="tags" placeholder="'.Lang::_('Tags, separetad with commas', 'posts').'" /><br/>'.
							 	'<fieldset>'.
							 		'<legend>'.Lang::_('Categories').'</legend>';
			
			}elseif($part == 'c'){
			
						echo 	'</fieldset>'.
							 '</div>'.
							 '<div id="dp_actions">'.
							 	'<input class="button" type="submit" name="save_draft" value="'.Lang::_('Save Draft').'" />&nbsp;'.
							 	'<input class="button" type="reset" value="'.Lang::_('Reset').'" />'.
							 	'<input class="button button_publish" type="submit" name="publish" value="'.Lang::_('Publish').'" />'.
							 	'<input type="hidden" name="action" value="create" />'.
							 	'<input type="hidden" name="status" value="publish" />'.
							 	'<input type="hidden" name="allow_comment" value="open" />'.
							 '</div>';
						
						parent::form('c');
						
				echo	'</div>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display a category label
			*
			* @static
			* @access	public
			* @param	int [$id]
			* @param	string [$name]
		*/
		
		public static function category($id, $name){
		
			echo '<span class="acat"><input id="cat'.$id.'" type="checkbox" name="category[]" value="'.$id.'"/><label for="cat'.$id.'">'.$name.'</label></span>';
		
		}
		
		/**
			* Display drafts widget structure
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function widget_drafts($part){
		
			if($part == 'o'){
			
				echo '<div id="dash_drafts" class="widget">'.
						'<div class="header">'.
							'<span>'.Lang::_('Drafts', 'dashboard').'</span>'.
							'<a class=toggle data-widget=dash_drafts>'.
								'<span class=bar></span>'.
							'</a>'.
							'<a class="button" href="'.Url::_(array('ns' => 'posts'), array('status' => 'draft')).'">'.Lang::_('View All', 'dashboard').'</a>'.
						'</div>'.
						'<div class="list">'.
							'<ul>';
			
			}elseif($part == 'c'){
			
				echo 		'</ul>'.
						'</div>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display a post in drafts list
			*
			* @static
			* @access	public
			* @param	int [$id]
			* @param	string [$title]
			* @param	string [$content]
			* @param	string [$date]
		*/
		
		public static function draft($id, $title, $content, $date){
		
			echo '<li>'.
					'<h4>'.
						'<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'edit'), array('action' => 'update', 'id' => $id)).'">'.$title.'</a> ('.date('d/m/Y @ H:i', strtotime($date)).')'.
					'</h4>'.
					'<p>'.substr(htmlspecialchars($content), 0, 500).'</p>'.
				 '</li>';
		
		}
		
		/**
			* Message displayed if no drafts
			*
			* @static
			* @access	public
		*/
		
		public static function no_draft(){
		
			echo '<li>'.Lang::_('No drafts', 'dashboard').'</li>';
		
		}
	
	}

?>