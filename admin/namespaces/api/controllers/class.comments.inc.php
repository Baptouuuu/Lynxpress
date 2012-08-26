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
	
	namespace Admin\Api\Controllers;
	use \Library\Variable\Get as VGet;
	use Exception;
	use \Library\Variable\Post as VPost;
	use \Library\Model\Comment;
	use \Library\File\File;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Controller to retrieve as JSON file a list of comments for a media or a post
		* Or if accessed in POST method you can create a new comment
		* Example:
		* <code>
		*	//This return the comments for the post with the id 1
		*	$comments = new Curl('http://example.com/admin/?ns=api&ctl=comments&type=post&id=1');
		*
		*	//This return the comments for the album with the id 2
		*	$comments = new Curl('http://example.com/admin/?ns=api&ctl=comments&type=media&id=1');
		*
		*	//Accessing this controller in POST method with an array as follows will create a new comment on the website
		*	array(
		*		'name' => 'name of the user',
		*		'email' => 'email of the user',
		*		'content' => 'message',
		*		'id' => 'id of the post or the media',
		*		'type' => 'post or media',
		*		'create' => true
		*	)
		* </code>
		*
		* @package		Admin
		* @subpackage	Api\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Comments extends Master{
	
		private $_comments = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->build_url();
			
			if(!$this->check_cache()){
			
				if(VPost::create()){
				
					$this->create();
				
				}else{
				
					$this->get_comments();
					
					$cache = new File();
					$cache->_content = $this->_comments;
					$cache->save($this->_cache);
				
				}
			
			}else{
			
				$this->_comments = File::read($this->_cache)->_content;
			
			}
		
		}
		
		/**
			* Build url cache
			*
			* @access	private
		*/
		
		private function build_url(){
		
			$this->_cache = parent::FOLDER.'comments-'.VGet::type().'-'.VGet::id().'.json';
		
		}
		
		/**
			* Retrieve comments
			*
			* @access	private
		*/
		
		private function get_comments(){
		
			try{
			
				if(!VGet::type() || !VGet::id())
					throw new Exception('Invalid request');
				
				$to_read['table'] = 'comment';
				$to_read['columns'] = array('_name', '_content', '_date');
				$to_read['condition_columns'][':ri'] = '_rel_id';
				$to_read['condition_select_types'][':ri'] = '=';
				$to_read['condition_values'][':ri'] = VGet::id();
				$to_read['value_types'][':ri'] = 'int';
				$to_read['condition_types'][':rt'] = 'AND';
				$to_read['condition_columns'][':rt'] = '_rel_type';
				$to_read['condition_select_types'][':rt'] = '=';
				$to_read['condition_values'][':rt'] = VGet::type();
				$to_read['value_types'][':rt'] = 'str';
				$to_read['condition_types'][':s'] = 'AND';
				$to_read['condition_columns'][':s'] = '_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'approved';
				$to_read['value_types'][':s'] = 'str';
				$to_read['order'] = array('_date', 'DESC');
				
				$this->_comments = json_encode($this->_db->read($to_read));
			
			}catch(Exception $e){
			
				$this->_action_msg .= $e->getMessage();
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			header('Content-type: application/json; charset=utf-8');
			
			if(!empty($this->_action_msg))
				echo json_encode(array('message' => $this->_action_msg));
			elseif(VPost::create())
				echo '{"message":true}';
			else
				echo $this->_comments;
		
		}
		
		/**
			* Create a new comment
			*
			* @access	private
		*/
		
		private function create(){
		
			try{
			
				if(!VPost::name() && !VPost::email() && !VPost::content() && !VPost::id() && !VPost::type())
					throw new Exception('Invalid request');
				
				if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , VPost::email()))
					throw new Exception('Invalid email');
				
				$c = new Comment();
				$c->_name = VPost::name();
				$c->_email = VPost::email();
				$c->_content = VPost::content();
				$c->_rel_id = VPost::id();
				$c->_rel_type = VPost::type();
				$c->_status = 'pending';
				
				$c->create();
			
			}catch(Exception $e){
			
				$this->_action_msg .= $e->getMessage();
			
			}
		
		}
	
	}

?>