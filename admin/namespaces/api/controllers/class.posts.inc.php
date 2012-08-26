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
	use \Library\Model\User;
	use \Library\File\File;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Controller to retrieve as JSON file a list of posts of the website
		* If no special parameter passed it will return the last 50 posts
		* If 'since' parameter passed it will return posts published since this date
		* If 'id' parameter passed it will return the post with the associated id
		* Example:
		* <code>
		*	//This return the last 50 posts
		*	$posts = new Curl('http://example.com/admin/?ns=api&ctl=posts');
		*
		*	//This will return posts published from October 19th, 1977
		*	$posts = new Curl('http://exmaple.com/admin/?ns=api&ctl=posts&since=1977-10-19');
		*
		*	//This will return the post with the id 1
		*	$first_post = new Curl('http://example.com/admin/?ns=api&ctl=posts&id=1');
		* </code>
		*
		* @package		Admin
		* @subpackage	Api\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Posts extends Master{
	
		private $_posts = null;
		private $_since = null;
		private $_id = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			if(VGet::since())
				$this->_since = VGet::since();
			elseif(VGet::id())
				$this->_id = VGet::id();
				
			$this->build_url();
			
			if(!$this->check_cache()){
			
				$this->get_posts();
				
				$this->_posts = json_encode($this->_posts);
				
				$cache = new File();
				$cache->_content = $this->_posts;
				$cache->save($this->_cache);
			
			}else{
			
				$this->_posts = File::read($this->_cache)->_content;
			
			}
		
		}
		
		/**
			* Build cache url
			*
			* @access	private
		*/
		
		private function build_url(){
		
			if(!empty($this->_since))
				$this->_cache = parent::FOLDER.'posts-since-'.$this->_since.'.json';
			elseif(!empty($this->_id))
				$this->_cache = parent::FOLDER.'post-'.$this->_id.'.json';
			else
				$this->_cache = parent::FOLDER.'posts.json';
		
		}
		
		/**
			* Retrieve wished posts
			*
			* @access	private
		*/
		
		private function get_posts(){
		
			try{
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('_id', '_title', '_content', '_allow_comment', '_date', '_user', '_tags', '_permalink', '_extra');
				$to_read['condition_columns'][':s'] = '_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				
				if(!empty($this->_since)){
				
					$to_read['condition_types'][':d'] = 'AND';
					$to_read['condition_columns'][':d'] = '_date';
					$to_read['condition_select_types'][':d'] = '>';
					$to_read['condition_values'][':d'] = $this->_since;
					$to_read['value_types'][':d'] = 'str';
				
				}elseif(!empty($this->_id)){
				
					$to_read['condition_types'][':i'] = 'AND';
					$to_read['condition_columns'][':i'] = '_id';
					$to_read['condition_select_types'][':i'] = '=';
					$to_read['condition_values'][':i'] = $this->_id;
					$to_read['value_types'][':i'] = 'int';
				
				}else{
				
					$to_read['limit'] = array(0, 50);
				
				}
				
				$to_read['order'] = array('_date', 'DESC');
				
				$this->_posts = $this->_db->read($to_read);
				
				if(!empty($this->_posts))
					foreach($this->_posts as &$p){
					
						$p['_extra'] = json_decode($p['_extra']);
						
						$user = new User($p['_user']);
						
						$p['_user'] = array('_id' => $user->_id, '_publicname' => $user->_publicname);
					
					}
			
			}catch(Exception $e){
			
				$this->_action_msg = $e->getMessage();
			
			}
		
		}
		
		/**
			* Display the list of posts as JSON file
			*
			* @access	public
		*/
		
		public function display_content(){
		
			header('Content-type: application/json; charset=utf-8');
			
			echo $this->_posts;
		
		}
	
	}

?>