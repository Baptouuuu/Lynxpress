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
		* Views for edit controller
		*
		* @package		Admin
		* @subpackage	Comments\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Edit extends Master{
	
		/**
			* Display the menu of comments edit page
			*
			* @static
			* @access	public
			* @param	boolean	[$allowed] If user is allowed to access this controller
			* @param	string	[$text] Text displayed depending of the current action
		*/
		
		public static function menu($allowed, $text = ''){
		
			if($allowed){
			
				echo '<div id="menu">'.
						'<span class="menu_item"><a href="'.Url::_(array('ns' => 'comments')).'">'.Lang::_('Comments').'</a></span>'.
						'<span id="menu_selected" class="menu_item"><a href="#">'.$text.'</a></span>'.
					 '</div>';
			
			}else{
			
				echo '<div id="menu">'.
						'<span id="menu_selected" class="menu_item"><a href="'.Url::_(array('ns' => 'comments')).'">'.Lang::_('Comments').'</a></span>'.
					 '</div>';
			
			}
		
		}
		
		/**
			* Display a reply form
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
		*/
		
		public static function reply($id, $name){
		
			echo '<section id="reply_form">'.
				 	'<textarea class="txta" name="content" autofocus placeholder="'.Lang::_('Your comment', 'comments').'" required>'.
				 		'@<a href="#comment_'.$id.'">'.$name.'</a>: '.
				 	'</textarea>'.
				 	'<div id="actions">'.
				 		'<a class="button" href="'.Url::_(array('ns' => 'comments')).'">Cancel</a>'.
				 		'<input class="button publish" type="submit" name="create" value="'.Lang::_('Reply').'" />'.
				 	'</div>'.
				 '</section>';
		
		}
		
		/**
			* Display an edit form
			*
			* @static
			* @access	public
			* @param	integer	[$id]
			* @param	string	[$name]
			* @param	string	[$email]
			* @param	string	[$content]
			* @param	string	[$status]
			* @param	string	[$date]
		*/
		
		public static function edit($id, $name, $email, $content, $status, $date){
		
			echo '<section id="comment_edit">'.
					'<div id="ce_user">'.
						'<div id="ceu_header">'.
							Lang::_('User').
						'</div>'.
						'<div id="ceu_content">'.
							'<input class="input" type="text" name="name" value="'.$name.'" placeholder="'.Lang::_('Name').'" required /><br/>'.
							'<input class="input" type="email" name="email" value="'.$email.'" placeholder="'.Lang::_('E-mail').'" required /> '.
							'<span class="indication">(<a href="mailto:'.$email.'">'.Lang::_('Contact').'</a>)</span>'.
						'</div>'.
					'</div>'.
					'<textarea class="txta" name="content" autofocus placeholder="'.Lang::_('Your comment', 'comments').'" required>'.$content.'</textarea>'.
					'<div id="ce_status">'.
						'<div id="ces_header">'.
							Lang::_('Status').
						'</div>'.
						'<div id="ces_content">'.
							'<div id="cesc_status">'.
								'<span>'.
									'<input id="cescs_approved" type="radio" name="status" value="approved" checked />'.
									'<label class="green" for="cescs_approved">'.Lang::_('Approved').'</label> '.
								'</span>'.
								'<span>'.
									'<input id="cescs_pending" type="radio" name="status" value="pending" />'.
									'<label class="orange" for="cescs_pending">'.Lang::_('Pending').'</label> '.
								'</span>'.
								'<span>'.
									'<input id="cescs_spam" type="radio" name="status" value="spam" />'.
									'<label class="red" for="cescs_spam">'.Lang::_('Spam').'</label> '.
								'</span>'.
							'</div>'.
							'<div id="cesc_date">'.
								Lang::_('Submitted on').' <span class="bold">'.date('d/m/Y @ H:i', strtotime($date)).'</span>'.
							'</div>'.
							'<div id="actions">'.
								'<a class="button delete" href="'.Url::_(array('ns' => 'comments'), array('status' => 'trash', 'action' => 'update', 'to' => 'trash', 'id' => $id)).'">'.Lang::_('Move to Trash').'</a>'.
								'<input class="button publish" type="submit" name="update" value="'.Lang::_('Update').'" />'.
							'</div>'.
						'</div>'.
					'</div>'.
				 '</section>';
		
		}
	
	}

?>