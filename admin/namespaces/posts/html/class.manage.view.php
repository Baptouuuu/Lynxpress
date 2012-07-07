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
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Manage extends Master{
	
		/**
			* Display the menu of posts manage page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span class="menu_item"><a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'edit')).'">'.Lang::_('Add').'</a></span>'.
				 	'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'posts')).'">'.Lang::_('Posts').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display an element in the submenu
			*
			* @static
			* @access	public
			* @param	string [$key]
			* @param	string [$name]
			* @param	int [$count]
			* @param	boolean [$selected]
		*/
		
		public static function status($key, $name, $count, $selected = false){
		
			echo '<span><a '.(($selected)?'class="selected"':'').' href="'.Url::_(array('ns' => 'posts'), array('status' => $key)).'">'.Lang::_(ucfirst($name)).'</a> ('.$count.')</span>';
		
		}
		
		/**
			* Display available actions for posts
			*
			* @static
			* @access	public
			* @param	string [$part]
			* @param	string [$status] Status currently viewed
		*/
		
		public static function actions($part, $status = 'all'){
		
			if($part == 'o'){
			
				echo '<div id="actions">';
				
				if($status !== 'trash'){
				
					echo '<input class="button" type="submit" name="trash" value="'.Lang::_('Move to Trash').'" />&nbsp;&nbsp;';
				
				}else{
				
					echo '<input class="button" type="submit" name="restore" value="'.Lang::_('Restore').'" /> '.
						 '<input class="button delete" type="submit" name="delete" value="'.Lang::_('Delete').'" data-confirm="'.Lang::_('Really').'?" /> '.
						 '<input class="button delete" type="submit" name="empty_trash" value="'.Lang::_('Empty Trash').'" data-confirm="'.Lang::_('Really').'?" />&nbsp;&nbsp;';
				
				}
						
				echo	'<select name="date">'.
							'<option value="all">'.Lang::_('Dates').'...</option>';
			
			}elseif($part == 'm'){
			
				echo	'</select> '.
						'<select name="category">'.
							'<option value="all">'.Lang::_('Categories').'...</option>';
			
			}elseif($part == 'c'){
			
				echo 	'</select> '.
						'<input class="button" type="submit" name="filter" value="'.Lang::_('Filter').'" />'.
						'<div id="search_box">'.
							'<input id="search_input" class="input" type="text" name="search" list="titles" placeholder="'.Lang::_('Posts').'" x-webkit-speech />'.
							'<input class="button" type="submit" name="search_button" value="'.Lang::_('Search').'" />'.
						'</div>'.
						'<input type="hidden" name="status" value="'.$status.'" />'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display posts table structure
			*
			* @static
			* @access	public
			* @param	string [$part]
		*/
		
		public static function table($part){
		
			if($part == 'o'){
			
				echo '<table id="table">'.
						'<thead>'.
							'<tr>'.
								'<th class="column_checkbox"><input class="check_all" data-select="post_id" type="checkbox" /></th>'.
								'<th class="column_title">'.Lang::_('Title').'</th>'.
								'<th class="column_user">'.Lang::_('User').'</th>'.
								'<th class="column_categories">'.Lang::_('Categories').'</th>'.
								'<th class="column_tags">'.Lang::_('Tags').'</th>'.
								'<th class="column_comments">'.Lang::_('Comments').'</th>'.
								'<th class="column_date">'.Lang::_('Date').'</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_checkbox"><input class="check_all" data-select="post_id" type="checkbox" /></th>'.
								'<th class="column_title">'.Lang::_('Title').'</th>'.
								'<th class="column_user">'.Lang::_('User').'</th>'.
								'<th class="column_categories">'.Lang::_('Categories').'</th>'.
								'<th class="column_tags">'.Lang::_('Tags').'</th>'.
								'<th class="column_comments">'.Lang::_('Comments').'</th>'.
								'<th class="column_date">'.Lang::_('Date').'</th>'.
							'</tr>'.
						'</tfoot>'.
						'<tbody>';
			
			}elseif($part == 'c'){
			
				echo 	'</tbody>'.
					 '</table>';
			
			}
		
		}
		
		/**
			* Display a post in data table
			*
			* @static
			* @access	public
			* @param	int [$id]
			* @param	string [$title]
			* @param	string [$date]
			* @param	object [$user]
			* @param	string [$status]
			* @param	string [$categories]
			* @param	string [$tags]
			* @param	string [$permalink]
			* @param	int [$comments]
		*/
		
		public static function post($id, $title, $date, $user, $status, $categories, $tags, $permalink, $comments){
		
			$draft = null;
			
			if($status == 'draft')
				$draft = '<span class="draft"> - '.Lang::_('Draft').'</span>';
			
			$actions = null;
			
			if($status != 'trash'){
			
				$actions = '<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'edit'), array('action' => 'update', 'id' => $id)).'">'.Lang::_('Edit').'</a> | ';
				$actions .= '<a class="red" href="'.Url::_(array('ns' => 'posts'), array('action' => 'trash', 'id' => $id, 'status' => $status)).'">'.Lang::_('Trash').'</a> | ';
				$actions .= '<a href="'.Url::_(array('ns' => 'posts', 'id' => $permalink), (($status == 'draft')?array('preview' => 'true'):array()), true).'">'.Lang::_('View').'</a>';
			
			}else{
			
				$actions = '<a class="orange" href="'.Url::_(array('ns' => 'posts'), array('action' => 'untrash', 'id' => $id, 'status' => $status)).'">'.Lang::_('Restore').'</a> | ';
				$actions .= '<a class="red" href="'.Url::_(array('ns' => 'posts'), array('action' => 'delete', 'id' => $id, 'status' => $status)).'">'.Lang::_('Delete').'</a>';
			
			}
			
			echo '<tr>'.
					'<th class="column_checkbox"><input class="post_id" type="checkbox" name="post_id[]" value="'.$id.'"></th>'.
					'<td class="column_title">'.
						'<a href="'.Url::_(array('ns' => 'posts', 'ctl' => 'edit'), array('action' => 'update', 'id' => $id)).'">'.$title.'</a>'.$draft.
						'<div class="row_actions">'.
							$actions.
						'</div>'.
					'</td>'.
					'<td class="column_user">'.
						'<a href="'.Url::_(array('ns' => 'posts'), array('user' => $user->_id)).'">'.$user->_username.'</a>'.
					'</td>'.
					'<td class="column_categories">'.
						$categories.
					'</td>'.
					'<td class="column_tags">'.
						$tags.
					'</td>'.
					'<td class="column_comments">'.
						$comments.
					'</td>'.
					'<td class="column_date">'.
						date('d/m/Y @ H:i', strtotime($date)).
					'</td>'.
				'</tr>';
		
		}
		
		/**
			* Message displayed if no posts to display
			*
			* @static
			* @access	public
		*/
		
		public static function no_post(){
		
			echo '<tr>'.
					'<td colspan="7">'.Lang::_('No post to display', 'posts').'</td>'.
				 '</tr>';
		
		}
	
	}

?>