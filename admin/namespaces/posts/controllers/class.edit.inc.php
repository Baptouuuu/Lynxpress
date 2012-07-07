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
	
	namespace Admin\Posts\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Posts\Html\Edit as Html;
	use Exception;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Request as VRequest;
	use \Library\Model\Post;
	use \Library\Model\User;
	use \Admin\Categories\Helpers\Categories;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Lang\Lang;
	use \Admin\Master\Helpers\Text;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Database\Database;
	use \Library\Model\Media;
	use \Library\Url\Url;
	use stdClass;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Edit controller for posts
		*
		* Create and edit posts with this controller
		*
		* @package		Admin
		* @subpackage	Posts\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Edit extends Master implements Controller{
	
		private $_post = null;
		private $_action = null;
		private $_categories = null;
		private $_pictures = null;
		private $_videos = null;
		private $_albums = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Manage Post';
			
			if($this->_user->_permissions->post){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/app.localStorage.js');
				Helper::add_header_link('js', WS_URL.'js/admin/core/model.post.js');
				Helper::add_header_link('js', WS_URL.'js/admin/core/view.posts.edit.js');
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.posts.edit.js');
				
				$this->get_post();
				$this->get_categories();
				$this->get_pictures();
				$this->get_videos();
				$this->get_albums();
				
				$this->create();
				$this->update();
			
			}
		
		}
		
		/**
			* Retrieve a post from database if there's an id given otherwise create a new one
			*
			* @access	private
		*/
		
		private function get_post(){
		
			if(VRequest::action() == 'update' && VRequest::id()){
			
				try{
				
					$this->_post = new Post(VRequest::id());
					$this->_action = 'update';
					
					Activity::log('started updating "'.$this->_post->_title.'"');
					
					$this->_post->_ouser = new User($this->_post->_user);
					
					$extra = $this->_post->_extra;
					
					if(!empty($extra))
						$this->_post->_extra = json_decode($extra);
				
				}catch(Exception $e){
				
					$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage);
					
					$this->_post = new Post();
					$this->_action = 'create';
					
					$this->_post->_date = date('Y-m-d H:i:s');
					$this->_post->_ouser = new User($this->_user->_id);
				
				}
			
			}else{
			
				$this->_post = new Post();
				$this->_action = 'create';
				
				$this->_post->_date = date('Y-m-d H:i:s');
				$this->_post->_ouser = new User($this->_user->_id);
			
			}
		
		}
		
		/**
			* Retrieve posts categories
			*
			* @access	private
		*/
		
		private function get_categories(){
		
			$this->_categories = Categories::get_type();
		
		}
		
		/**
			* Retrieve pictures
			*
			* @access	private
		*/
		
		private function get_pictures(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('*');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = 'LIKE';
				$to_read['condition_values'][':t'] = 'image%';
				$to_read['value_types'][':t'] = 'str';
				$to_read['condition_types'][':at'] = 'AND';
				$to_read['condition_columns'][':at'] = '_attach_type';
				$to_read['condition_select_types'][':at'] = '=';
				$to_read['condition_values'][':at'] = 'none';
				$to_read['value_types'][':at'] = 'str';
				
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array(0, 50);
				
				$this->_pictures = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve videos and flash fallbacks
			*
			* @access	private
		*/
		
		private function get_videos(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('*');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = 'LIKE';
				$to_read['condition_values'][':t'] = 'video%';
				$to_read['value_types'][':t'] = 'str';
				
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array(0, 50);
				
				$this->_videos = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
				
				if(!empty($this->_videos))
					foreach($this->_videos as &$v){
					
						$f = $v->_attach_type;
						
						if($f == 'fallback')
							$v->_fallback = new Media($v->_attachment);
					
					}
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve albums
			*
			* @access	private
		*/
		
		private function get_albums(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('*');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'album';
				$to_read['value_types'][':t'] = 'str';
				
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array(0, 50);
				
				$this->_albums = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			if($this->_action == 'update')
				Html::menu(true);
			else
				Html::menu();
		
		}
		
		/**
			* Display actions given to page and post status
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			Html::actions('o');
			
			if($this->_action == 'create'){
			
				Html::save_draft();
				echo Html::clear_localstorage();
				Html::publish();
			
			}elseif($this->_action == 'update'){
			
				if($this->_post->_status == 'draft'){
				
					Html::preview($this->_post->_permalink);
					Html::save_draft();
					echo Html::clear_localstorage();
					Html::publish();
				
				}elseif($this->_post->_status == 'publish'){
				
					Html::view($this->_post->_permalink);
					echo Html::clear_localstorage();
					Html::update();
				
				}
			
			}
			
			Html::actions('c');
		
		}
		
		/**
			* Display post form
			*
			* @access	private
		*/
		
		private function display_post(){
		
			Html::post_wrapper('o');
			
			$display_permalink = false;
			
			if($this->_action == 'update')
				$display_permalink = true;
			
			Html::post(
				$this->_post->_id,
				$this->_post->_title,
				$this->_post->_content,
				$this->_post->_permalink,
				$display_permalink,
				$this->_post->_status
			);
			
			$categories = explode(',', $this->_post->_category);
			
			if(!empty($this->_categories))
				foreach($this->_categories as $c)
					Html::category($c->_id, $c->_name, ((in_array($c->_id, $categories))?true:false));
			
			Html::tags($this->_post->_tags);
			
			Html::infos(
				$this->_post->_id,
				$this->_post->_allow_comment,
				$this->_post->_date,
				$this->_post->_ouser,
				$this->_action
			);
			
			$extra = $this->_post->_extra;
			
			if(!empty($extra)){
			
				$banner = $extra->banner;
				$gallery = $extra->gallery;
			
			}else{
			
				$banner = 0;
				$gallery = 0;
			
			}
			
			Html::extra();
			
			Html::popups(
				$gallery,
				$banner
			);
			
			Html::post_wrapper('c');
			
			Html::media_datalists(
				$this->_pictures,
				$this->_videos,
				$this->_albums,
				$banner,
				$gallery
			);
			
			Html::media_templates();
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			Html::noscript();
			
			if($this->_user->_permissions->post){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'posts', 'ctl' => 'edit')));
				
				$this->display_actions();
				$this->display_post();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Check if post data are valid or not
			*
			* @access	private
			* @return	boolean
		*/
		
		private function check_data(){
		
			$errors = array();
			
			if($this->_post->__set('_title', VPost::title()) !== true)
				$errors[] = Lang::_($this->_post->__set('_title', VPost::title()), 'posts');
			else
				$this->_post->_title = VPost::title();
			
			if($this->_post->__set('_content', VPost::content()) !== true)
				$errors[] = Lang::_($this->_post->__set('_content', VPost::content()), 'posts');
			else
				$this->_post->_content = VPost::content();
			
			if($this->_post->__set('_category', implode(',', VPost::category(array()))) !== true)
				$errors[] = Lang::_($this->_post->__set('_category', implode(',', VPost::category(array()))), 'posts');
			else
				$this->_post->_category = implode(',', VPost::category());
			
			if($this->_post->__set('_tags', VPost::tags()) !== true)
				$errors[] = Lang::_($this->_post->__set('_tags', VPost::tags()), 'posts');
			else
				$this->_post->_tags = VPost::tags('diverse');
			
			if(!empty($errors)){
			
				$this->_action_msg .= ActionMessages::errors($errors);
				return false;
			
			}else{
			
				return true;
			
			}
		
		}
		
		/**
			* Create a new post in database
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::action() == 'create' && (VPost::save_draft() || VPost::publish()) && $this->check_data()){
			
				try{
				
					if(VPost::allow_comment())
						$this->_post->_allow_comment = 'open';
					else
						$this->_post->_allow_comment = 'closed';
					
					$this->_post->_user = $this->_user->_id;
					
					if(VPost::save_draft())
						$this->_post->_status = 'draft';
					elseif(VPost::publish())
						$this->_post->_status = 'publish';
						
					$this->_post->_permalink = Text::slug($this->_post->_title);
					
					//check if permalink already used
					try{
					
						$post = new Post($this->_post->_permalink, '_permalink');
						$id = $post->_id;
						
						if(!empty($id))
							throw new Exception('same permalink found');
					
					}catch(Exception $e){
					
						if($e->getMessage() == 'same permalink found')
							throw new Exception(Lang::_('Generated permalink already used, please change your title!', 'posts'));
					
					}
					
					$extra = array();
					
					$extra['banner'] = VPost::banner();
					$extra['gallery'] = VPost::gallery();
					
					$this->_post->_extra = json_encode($extra);
					
					$this->_post->create();
					
					Activity::log('created the post "'.$this->_post->_title.'"');
					
					if(!empty($extra))
						$this->_post->_extra = json_decode($this->_post->_extra);
					
					$result = true;
					$this->_action = 'update';
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
					$this->_action = 'create';
				
				}
				
				$this->_action_msg .= ActionMessages::created($result);
			
			}
		
		}
		
		/**
			* Update a post
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::action() == 'update' && (VPost::save_draft() || VPost::publish() || VPost::update()) && $this->check_data()){
			
				try{
				
					if(VPost::allow_comment())
						$this->_post->_allow_comment = 'open';
					else
						$this->_post->_allow_comment = 'closed';
					
					if($this->_post->_user != $this->_user->_id){
					
						$this->_post->_updated = 1;
						$this->_post->_update_user = $this->_user->_id;
					
					}
					
					if(VPost::publish() && $this->_post->_status == 'draft')
						$this->_post->_date = date('Y-m-d H:i:s');
					
					if(VPost::save_draft())
						$this->_post->_status = 'draft';
					elseif(VPost::publish() || VPost::update())
						$this->_post->_status = 'publish';
					
					$extra = $this->_post->_extra;
					
					$extra->banner = VPost::banner();
					$extra->gallery = VPost::gallery();
					
					$this->_post->_extra = json_encode($extra);
					$this->_post->update('_extra');
					$this->_post->_extra = json_decode($this->_post->_extra);
					
					$this->_post->update('_title');
					$this->_post->update('_content');
					$this->_post->update('_allow_comment');
					$this->_post->update('_date');
					$this->_post->update('_status');
					$this->_post->update('_category');
					$this->_post->update('_tags');
					$this->_post->update('_updated', 'int');
					$this->_post->update('_update_user');
					
					Activity::log('updated the post "'.$this->_post->_title.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
	
	}

?>