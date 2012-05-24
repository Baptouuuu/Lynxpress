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
	
	namespace Admin\Comments\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang;
	use \Admin\Comments\Html\Edit as Html;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Model\Comment;
	use Exception;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Edit controller allows to reply to a comment or to edit one
		*
		* @package		Admin
		* @subpackage	Comments\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Edit extends Master implements Controller{
	
		private $_comment = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Comment');
			
			if($this->_user->_permissions->comment){
			
				$this->build_title();
				
				$this->get_comment();
				
				$this->create();
				$this->update();
			
			}
		
		}
		
		/**
			* Retrieve whished comment
			*
			* @access	private
		*/
		
		private function get_comment(){
		
			try{
			
				if(!VGet::id())
					throw new Exception(Lang::_('Comment id missing', 'comments'));
				
				$this->_comment = new Comment(VGet::id());
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				$this->_comment = new Comment();
			
			}
		
		}
		
		/**
			* Build the page title
			*
			* @access	private
		*/
		
		private function build_title(){
		
			if(VGet::action() == 'update')
				$this->_title = Lang::_('Comment edition', 'comments');
			elseif(VGet::action() == 'create')
				$this->_title = Lang::_('Comment reply', 'comments');
		
		}
		
		/**
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if(VGet::action() == 'update')
				Html::menu(true, Lang::_('Editing'));
			elseif(VGet::action() == 'create')
				Html::menu(true, Lang::_('Replying', 'comments'));
			else
				Html::menu(false);
		
		}
		
		/**
			* Display a reply form
			*
			* @access	private
		*/
		
		private function display_reply(){
		
			Html::reply(
				$this->_comment->_id,
				$this->_comment->_name
			);
		
		}
		
		/**
			* Display comment edit form
			*
			* @access	private
		*/
		
		private function display_edit(){
		
			Html::edit(
				$this->_comment->_id,
				$this->_comment->_name,
				$this->_comment->_email,
				$this->_comment->_content,
				$this->_comment->_status,
				$this->_comment->_date
			);
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			Html::noscript();
			
			if($this->_user->_permissions->comment){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'comments', 'ctl' => 'edit'), array('action' => VGet::action(), 'id' => VGet::id())));
				
				if(VGet::action() == 'create')
					$this->display_reply();
				elseif(VGet::action() == 'update')
					$this->display_edit();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Create a comment in reponse to the wished comment
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::create()){
			
				try{
				
					$c = new Comment();
					$c->_name = $this->_user->_publicname;
					$c->_email = $this->_user->_email;
					$c->_content = VPost::content();
					$c->_rel_type = $this->_comment->_rel_type;
					$c->_rel_id = $this->_comment->_rel_id;
					$c->_status = 'approved';
					
					$c->create();
					
					header('Location: '.Url::_(array('ns' => 'comments'), array('status' => 'approved')));
					exit;
				
				}catch(Exception $e){
				
					$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
				
				}
			
			}
		
		}
		
		/**
			* Update a comment
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update()){
			
				try{
				
					$this->_comment->_name = VPost::name();
					$this->_comment->_email = VPost::email();
					$this->_comment->_content = VPost::content();
					$this->_comment->_status = VPost::status();
					
					$this->_comment->update('_name');
					$this->_comment->update('_email');
					$this->_comment->update('_content');
					$this->_comment->update('_status');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>