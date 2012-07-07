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
	
	namespace Admin\Comments\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for manage controller
		*
		* @package		Admin
		* @subpackage	Comments\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Manage extends Master{
	
		/**
			* Display the menu of comments page
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'comments')).'">'.Lang::_('Comments').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display an element in the submenu
			*
			* @static
			* @access	public
			* @param	string	[$key]
			* @param	string	[$name]
			* @param	integer	[$count]
			* @param	boolean	[$selected]
		*/
		
		public static function status($key, $name, $count, $selected = false){
		
			echo '<span><a '.(($selected)?'class="selected"':'').' href="'.Url::_(array('ns' => 'comments'), array('status' => $key)).'">'.ucfirst($name).'</a> ('.$count.')</span>';
		
		}
		
		/**
			* Display actions structure
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function actions($part){
		
			if($part == 'o'){
			
				echo '<div id="actions">'.
						'<select name="action">'.
							'<option value="no">'.Lang::_('Actions').'...</option>';
			
			}elseif($part == 'c'){
			
				echo	'</select> '.
						'<input class="button" type="submit" name="apply" value="'.Lang::_('Apply').'" />'.
						'<div id="search_box">'.
							'<input id="search_input" class="input" type="text" name="search" list="titles" placeholder="'.Lang::_('Comments').'" x-webkit-speech />'.
							'<input class="button" type="submit" name="search_button" value="'.Lang::_('Search').'" />'.
						'</div>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display a button to empty spams
			*
			* @static
			* @access	public
		*/
		
		public static function empty_spam(){
		
			echo '<input class="button delete" type="submit" name="empty_spam" value="'.Lang::_('Empty Spam', 'comments').'" data-confirm="'.Lang::_('Really').'?" />';
		
		}
		
		/**
			* Display a button to empty trash
			*
			* @static
			* @access	public
		*/
		
		public static function empty_trash(){
		
			echo '<input class="button delete" type="submit" name="empty_trash" value="'.Lang::_('Empty Trash').'" data-confirm="'.Lang::_('Really').'?" />';
		
		}
		
		/**
			* Display comments table structure
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
								'<th class="column_checkbox"><input class="check_all" data-select="comment_id" type="checkbox" /></th>'.
								'<th class="column_user">'.Lang::_('User').'</th>'.
								'<th class="column_comment">'.Lang::_('Comment').'</th>'.
								'<th class="column_response">'.Lang::_('In Response To').'</th>'.
								'<th class="column_date">'.Lang::_('Date').'</th>'.
							'</tr>'.
						'</thead>'.
						'<tfoot>'.
							'<tr>'.
								'<th class="column_checkbox"><input class="check_all" data-select="comment_id" type="checkbox" /></th>'.
								'<th class="column_user">'.Lang::_('User').'</th>'.
								'<th class="column_comment">'.Lang::_('Comment').'</th>'.
								'<th class="column_response">'.Lang::_('In Response To').'</th>'.
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
			* Display a comment in the table
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$email]
			* @param	string	[$content]
			* @param	string	[$status]
			* @param	string	[$date]
			* @param	object	[$rel]
		*/
		
		public static function comment($id, $name, $email, $content, $status, $date, $rel){
		
			$approve = '<a class="green" href="'.Url::_(array('ns' => 'comments'), array('status' => $status, 'action' => 'update', 'to' => 'approved', 'id' => $id)).'">'.Lang::_('Approve').'</a>';
			$edit = '<a href="'.Url::_(array('ns' => 'comments', 'ctl' => 'edit'), array('action' => 'update', 'id' => $id)).'">'.Lang::_('Edit').'</a>';
			$spam = '<a class="orange" href="'.Url::_(array('ns' => 'comments'), array('status' => $status, 'action' => 'update', 'to' => 'spam', 'id' => $id)).'">'.Lang::_('Spam').'</a>';
			$trash = '<a class="red" href="'.Url::_(array('ns' => 'comments'), array('status' => $status, 'action' => 'update', 'to' => 'trash', 'id' => $id)).'">'.Lang::_('Trash').'</a>';
			$unapprove = '<a class="orange" href="'.Url::_(array('ns' => 'comments'), array('status' => $status, 'action' => 'update', 'to' => 'pending', 'id' => $id)).'">'.Lang::_('Unapprove', 'comments').'</a>';
			$reply = '<a href="'.Url::_(array('ns' => 'comments', 'ctl' => 'edit'), array('action' => 'create', 'id' => $id)).'">'.Lang::_('Reply').'</a>';
			$unspam = '<a class="orange" href="'.Url::_(array('ns' => 'comments'), array('status' => $status, 'action' => 'update', 'to' => 'pending', 'id' => $id)).'">'.Lang::_('Not Spam', 'comments').'</a>';
			$delete = '<a class="red" href="'.Url::_(array('ns' => 'comments'), array('status' => $status, 'action' => 'delete', 'id' => $id)).'">'.Lang::_('Delete').'</a>';
			$restore = '<a class="orange" href="'.Url::_(array('ns' => 'comments'), array('status' => $status, 'action' => 'update', 'to' => 'pending', 'id' => $id)).'">'.Lang::_('Restore').'</a>';
			
			switch($status){
			
				case 'pending':
					$actions = $approve.' | '.$edit.' | '.$spam.' | '.$trash;
					break;
				
				case 'approved':
					$actions = $unapprove.' | '.$reply.' | '.$edit.' | '.$spam.' | '.$trash;
					break;
				
				case 'spam':
					$actions = $edit.' | '.$unspam.' | '.$delete;
					break;
				
				case 'trash':
					$actions = $restore.' | '.$delete;
					break;
			
			}
			
			echo '<tr>'.
				 	'<th class="column_checkbox"><input class="comment_id" type="checkbox" name="comment_id[]" value="'.$id.'" /></th>'.
				 	'<td class="column_user">'.
				 		$name.'<br/>'.
				 		'<a href="mailto:'.$email.'">'.$email.'</a>'.
				 	'</td>'.
				 	'<td class="column_comment">'.
				 		htmlspecialchars(nl2br($content)).'<br/>'.
				 		'<div class="row_actions">'.
				 			$actions.
				 		'</div>'.
				 	'</td>'.
				 	'<td class="column_response">'.
				 		'<a href="'.$rel->_permalink.'" target="_blank">'.$rel->_name.'</a><br/>'.
				 		'<a href="'.Url::_(array('ns' => 'comments'), array('status' => $status, 'rel_type' => $rel->_type, 'rel_id' => $rel->_id)).'">'.Lang::_('All Comments').'</a>'.
				 	'</td>'.
				 	'<td class="column_date">'.
				 		date('d/m/Y @ H:i', strtotime($date)).
				 	'</td>'.
				 '</tr>';
		
		}
		
		/**
			* Message displayed if no comments found
			*
			* @static
			* @access	public
		*/
		
		public static function no_comment(){
		
			echo '<tr><td colspan="5">'.Lang::_('No comments found', 'comments').'</td></tr>';		
		}
	
	}

?>