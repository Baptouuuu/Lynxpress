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
	use \Admin\Master\Controllers\Controller;
	use \Library\Lang\Lang;
	use Exception;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Posts\Html\Manage as Html;
	use \Library\Database\Database;
	use \Admin\Categories\Helpers\Categories;
	use \Library\Model\User;
	use \Library\Model\Category;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Request as VRequest;
	use \Library\Variable\Get as VGet;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Model\Post;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Url\Url;
	use \Admin\Comments\Helpers\Comments;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Controller to manage posts
		*
		* Browse, and manipulate posts of the website
		*
		* @package		Admin
		* @subpackage	Posts\Controller
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Controller{
	
		private $_posts = null;
		private $_status = null;
		private $_infos = null;
		private $_categories = null;
		private $_dates = null;
		const ITEMS = 20;
		private $_page = null;
		private $_limit_start = null;
		private $_max = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Posts');
			
			if($this->_user->_permissions->post){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.table.js');
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.button_confirm.js');
				
				if(VRequest::status(false) && !VPost::empty_trash(false))
					$this->_status = VRequest::status();
				else
					$this->_status = 'all';
			
				$this->update();
				$this->delete();
				
				$this->get_posts();
				$this->get_infos();
				$this->get_categories();
			
			}
		
		}
		
		/**
			* Retrieve posts from database
			*
			* @access	private
		*/
		
		private function get_posts(){
		
			try{
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('_id', '_title', '_date', '_user', '_status', '_category', '_tags', '_permalink');
				
				if((VPost::search_button() && VPost::search()) || VGet::search()){
				
					$search = '%'.trim(VRequest::search()).'%';
					
					$to_read['condition_columns']['group'][':t'] = '_title';
					$to_read['condition_select_types'][':t'] = 'LIKE';
					$to_read['condition_values'][':t'] = $search;
					$to_read['value_types'][':t'] = 'str';
					$to_read['condition_types'][':c'] = 'OR';
					$to_read['condition_columns']['group'][':c'] = '_content';
					$to_read['condition_select_types'][':c'] = 'LIKE';
					$to_read['condition_values'][':c'] = $search;
					$to_read['value_types'][':c'] = 'str';
					$to_read['condition_types'][':tags'] = 'OR';
					$to_read['condition_columns']['group'][':tags'] = '_tags';
					$to_read['condition_select_types'][':tags'] = 'LIKE';
					$to_read['condition_values'][':tags'] = $search;
					$to_read['value_types'][':tags'] = 'str';
					$to_read['condition_types'][':s'] = 'AND';
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['value_types'][':s'] = 'str';
					
					if($this->_status == 'all'){
					
						$to_read['condition_select_types'][':s'] = '!=';
						$to_read['condition_values'][':s'] = 'trash';
					
					}else{
					
						$to_read['condition_select_types'][':s'] = '=';
						$to_read['condition_values'][':s'] = $this->_status;
					
					}
					
				
				}elseif(VRequest::filter(false) && (VRequest::date() !== 'all' || VRequest::category() !== 'all')){
				
					if(VRequest::date() !== 'all' && VRequest::category() !== 'all'){
					
						$to_read['condition_columns'][':d'] = '_date';
						$to_read['condition_select_types'][':d'] = 'LIKE';
						$to_read['condition_values'][':d'] = '%'.VRequest::date().'%';
						$to_read['value_types'][':d'] = 'str';
						$to_read['condition_types'][':c'] = 'AND';
						$to_read['condition_columns'][':c'] = '_category';
						$to_read['condition_select_types'][':c'] = 'LIKE';
						$to_read['condition_values'][':c'] = '%"'.VRequest::category().'"%';
						$to_read['value_types'][':c'] = 'str';
					
					}elseif(VRequest::date() !== 'all'){
					
						$to_read['condition_columns'][':d'] = '_date';
						$to_read['condition_select_types'][':d'] = 'LIKE';
						$to_read['condition_values'][':d'] = '%'.VRequest::date().'%';
						$to_read['value_types'][':d'] = 'str';
					
					}elseif(VRequest::category() !== 'all'){
					
						$to_read['condition_columns'][':c'] = '_category';
						$to_read['condition_select_types'][':c'] = 'LIKE';
						$to_read['condition_values'][':c'] = '%"'.VRequest::category().'"%';
						$to_read['value_types'][':c'] = 'str';
					
					}
					
					$to_read['condition_types'][':s'] = 'AND';
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['value_types'][':s'] = 'str';
					
					if($this->_status == 'all'){
					
						$to_read['condition_select_types'][':s'] = '!=';
						$to_read['condition_values'][':s'] = 'trash';
					
					}else{
					
						$to_read['condition_select_types'][':s'] = '=';
						$to_read['condition_values'][':s'] = $this->_status;
					
					}
				
				}elseif(VGet::user()){
				
					$to_read['condition_columns'][':u'] = '_user';
					$to_read['condition_select_types'][':u'] = '=';
					$to_read['condition_values'][':u'] = VGet::user();
					$to_read['value_types'][':u'] = 'str';
					$to_read['condition_types'][':s'] = 'AND';
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['condition_select_types'][':s'] = '!=';
					$to_read['condition_values'][':s'] = 'trash';
					$to_read['value_types'][':s'] = 'str';
				
				}elseif($this->_status != 'all'){
				
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['condition_select_types'][':s'] = '=';
					$to_read['condition_values'][':s'] = $this->_status;
					$to_read['value_types'][':s'] = 'str';
				
				}else{
				
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['condition_select_types'][':s'] = '!=';
					$to_read['condition_values'][':s'] = 'trash';
					$to_read['value_types'][':s'] = 'str';
				
				}
				
				//pass $to_read by parameter to have same conditions
				$this->get_pagination($to_read);
				$this->get_dates($to_read);
				
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array($this->_limit_start, self::ITEMS);
				
				$this->_posts = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Post');
				
				if(!empty($this->_posts))
					foreach($this->_posts as &$p){
					
						$user = new User();
						$user->_id = $p->_user;
						$user->read('_username');
						
						$p->_user = $user;
						
						$cats = json_decode($p->_category, true);
						$category = array();
						
						if(!empty($cats))
							foreach($cats as $c){
							
								$cat = new Category();
								$cat->_id = $c;
								$cat->read('_name');
								
								$category[] = $cat->_name;
							
							}
						
						$p->_category = implode(', ', $category);
						
						$to_read = null;
						$to_read['table'] = 'comment';
						$to_read['columns'] = array('COUNT(_id) as count');
						$to_read['condition_columns'][':r'] = '_rel_id';
						$to_read['condition_select_types'][':r'] = '=';
						$to_read['condition_values'][':r'] = $p->_id;
						$to_read['value_types'][':r'] = 'str';
						$to_read['condition_types'][':t'] = 'AND';
						$to_read['condition_columns'][':t'] = '_rel_type';
						$to_read['condition_select_types'][':t'] = '=';
						$to_read['condition_values'][':t'] = 'post';
						$to_read['value_types'][':t'] = 'str';
						
						$comments = $this->_db->read($to_read);
						
						if(empty($comments))
							$p->_comments = 0;
						else
							$p->_comments = $comments[0]['count'];
					
					}
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Get pagination informations
			*
			* @access	private
			* @param	array [$to_read]
		*/
		
		private function get_pagination($to_read){
		
			try{
			
				list($this->_page, $this->_limit_start) = Helper::pagination(self::ITEMS);
				
				$to_read['columns'] = array('COUNT(_id) as count');
				
				$count = $this->_db->read($to_read);
				
				$this->_max = ceil($count[0]['count']/self::ITEMS);
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve posts dates
			*
			* @access	private
			* @param	array [$to_read] Array from get_posts
		*/
		
		private function get_dates($to_read){
		
			try{
			
				$to_read['columns'] = array('distinct substr(_date, 1, 7) as _date');
				$to_read['order'] = array('_date', 'DESC');
				
				if(VRequest::date('all') != 'all'){
				
					unset($to_read['condition_columns'][':d']);
					unset($to_read['condition_select_types'][':d']);
					unset($to_read['condition_values'][':d']);
					unset($to_read['value_types'][':d']);
				
				}
				
				$this->_dates = $this->_db->read($to_read);
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve all posts status
			*
			* @access	private
		*/
		
		private function get_infos(){
		
			try{
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('_status', 'COUNT(_status) as count');
				$to_read['groupby'] = '_status';
				
				$infos = $this->_db->read($to_read);
				
				$statuses = array('all' => 0, 'publish' => 0, 'draft' => 0, 'trash' =>0);
				
				foreach($statuses as $key => &$stat)
					if(!empty($infos))
						foreach($infos as $i)
							if($i['_status'] == $key)
								$stat = $i['count'];
				
				$statuses['all'] = $statuses['publish'] + $statuses['draft'];
				
				$this->_infos = $statuses;
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
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
			* Display page title
			*
			* @access	private
		*/
		
		private function display_title(){
		
			Html::menu();
		
		}
		
		/**
			* Display statuses links
			*
			* @access	private
		*/
		
		private function display_statuses(){
		
			Html::submenu('o');
			
			foreach($this->_infos as $status => $count){
			
				$selected = false;
				
				if($this->_status == $status)
					$selected = true;
				
				//tweak for status traduction
				Html::status($status, (($status == 'publish')?'published':$status), $count, $selected);
			
			}
			
			Html::submenu('c');
		
		}
		
		/**
			* Display posts actions
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			$dates = array();
			
			if(!empty($this->_dates))
				foreach($this->_dates as $d)
					$dates[$d['_date']] = date('F Y', strtotime($d['_date']));
			
			Html::actions('o', $this->_status);
			
			foreach($dates as $key => $d)
				Html::option($key, $d);
			
			Html::actions('m');
			
			if(!empty($this->_categories))
				foreach($this->_categories as $c)
					Html::option($c->_id, $c->_name);
			
			Html::actions('c', $this->_status);
		
		}
		
		/**
			* Display posts table
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::table('o');
			
			if(!empty($this->_posts))
				foreach($this->_posts as $p)
					Html::post(
						$p->_id,
						$p->_title,
						$p->_date,
						$p->_user,
						$p->_status,
						$p->_category,
						$p->_tags,
						$p->_permalink,
						$p->_comments
					);
			else
				Html::no_post();
			
			Html::table('c');
		
		}
		
		/**
			* Display pagination
			*
			* @access	private
		*/
		
		private function display_pagination(){
		
			if($this->_max > 1){
			
				$link = array();
				
				if(VRequest::filter(false))
					$link = array('filter' => 'true', 'date' => VRequest::date(), 'category' => VRequest::category(), 'status' => $this->_status);
				elseif(VRequest::search())
					$link = array('search' => trim(VRequest::search()), 'status' => $this->_status);
				elseif(VGet::user())
					$link = array('user' => VGet::user());
				elseif($this->_status != 'all')
					$link = array('status' => $this->_status);
				
				Html::pagination($this->_page, $this->_max, $link);
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_title();
			
			Html::noscript();
			
			if($this->_user->_permissions->post){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'posts')));
				
				$this->display_statuses();
				$this->display_actions();
				
				$this->display_table();
				
				$this->display_pagination();
				
				//create a datalist for search input
				echo Helper::datalist('titles', $this->_posts, '_title');
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update posts statuses
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VGet::action() == 'trash' && VGet::id()){
			
				try{
				
					$post = new Post();
					$post->_id = VGet::id();
					$post->read('_title');
					$post->_status = 'trash';
					$post->update('_status');
					
					Activity::log('trashed the post "'.$post->_title.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::trashed($result);
			
			}elseif(VGet::action() == 'untrash' && VGet::id()){
			
				try{
				
					$post = new Post();
					$post->_id = VGet::id();
					$post->read('_title');
					$post->_status = 'draft';
					$post->update('_status');
					
					Activity::log('restored the post "'.$post->_title.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::restored($result);
			
			}elseif(VPost::trash() && VPost::post_id()){
			
				try{
				
					foreach(VPost::post_id() as $id){
					
						$p = new Post();
						$p->_id = $id;
						$p->read('_title');
						$p->_status = 'trash';
						$p->update('_status');
						
						Activity::log('trashed the post "'.$p->_title.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::trashed($result);
			
			}elseif(VPost::restore() && VPost::post_id()){
			
				try{
				
					foreach(VPost::post_id() as $id){
					
						$p = new Post();
						$p->_id = $id;
						$p->read('_title');
						$p->_status = 'draft';
						$p->update('_status');
						
						Activity::log('restored the post "'.$p->_title.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::restored($result);
			
			}
		
		}
		
		/**
			* Delete posts
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VGet::action() == 'delete' && VGet::id() && $this->_user->_permissions->delete){
			
				try{
				
					$post = new Post();
					$post->_id = VGet::id();
					$post->read('_title');
					
					$post->delete();
					
					Comments::delete_for($post->_id, 'post');
					
					Activity::log('deleted the post "'.$post->_title.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VPost::delete() && VPost::post_id() && $this->_user->_permissions->delete){
			
				try{
				
					foreach(VPost::post_id() as $id){
					
						$p = new Post();
						$p->_id = $id;
						$p->read('_title');
						
						$p->delete();
						
						Comments::delete_for($p->_id, 'post');
						
						Activity::log('deleted the post "'.$p->_title.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VPost::empty_trash() && $this->_user->_permissions->delete){
			
				try{
				
					$to_read['table'] = 'post';
					$to_read['columns'] = array('_id', '_title');
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['condition_select_types'][':s'] = '=';
					$to_read['condition_values'][':s'] = 'trash';
					$to_read['value_types'][':s'] = 'str';
					
					$posts = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Post');
					
					if(!empty($posts))
						foreach($posts as $p){
						
							$p->delete();
							
							Comments::delete_for($p->_id, 'post');
							
							Activity::log('deleted the post "'.$p->_title.'"');
						
						}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif((VGet::action() == 'delete' || VPost::delete() || VPost::empty_trash()) && !$this->_user->_permissions->delete){
			
				$this->_action_msg .= ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>