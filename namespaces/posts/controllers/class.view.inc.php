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
	
	namespace Site\Posts\Controllers;
	use \Site\Master\Controllers\Controller;
	use \Site\Templates\Helpers\Template;
	use Exception;
	use \Library\Variable\Get as VGet;
	use \Library\Model\Post;
	use \Library\Model\User;
	use \Library\Model\Category;
	use \Library\Model\Media;
	use \Library\Database\Database;
	use \Site\Posts\Html\View as Html;
	use \Site\Master\Helpers\Document;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Display a specific post with its comments
		*
		* @package		Site
		* @subpackage	Posts\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class View extends Controller{
	
		private $_post = null;
		private $_tags = null;
		private $_comments = null;
		private $_related_posts = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->get_post();
			$this->get_comments();
			$this->get_related_posts();
			
			$this->_title = $this->_post->_title;
			
			Template::publish('posts.view.'.$this->_post->_permalink.(($this->_post->_allow_comment == 'open')?'.comments':''));
		
		}
		
		/**
			* Retrieve wished post
			*
			* @access	private
		*/
		
		private function get_post(){
		
			try{
			
				if(!VGet::id(false))
					throw new Exception('Trying to access unspecified post');
				
				$this->_post = new Post(VGet::id(), '_permalink');
				
				$user = new User();
				$user->_id = $this->_post->_user;
				$user->read('_publicname');
				$user->read('_email');
				$user->read('_website');
				$user->read('_msn');
				$user->read('_twitter');
				$user->read('_facebook');
				$user->read('_google');
				$user->read('_bio');
				
				$this->_post->_user = $user;
				
				$cats = json_decode($this->_post->_category, true);
				
				foreach($cats as &$c)
					$c = new Category($c);
				
				$this->_post->_category = $cats;
				
				$this->_tags = explode(',', $this->_post->_tags);
				
				$extra = json_decode($this->_post->_extra);
				
				if(!empty($extra->banner))
					$extra->banner = new Media($extra->banner);
				
				if(!empty($extra->gallery)){
				
					$extra->gallery = new Media($extra->gallery);
					
					$to_read['table'] = 'media';
					$to_read['columns'] = array('*');
					$to_read['condition_columns'][':a'] = '_attachment';
					$to_read['condition_select_types'][':a'] = '=';
					$to_read['condition_values'][':a'] = $extra->gallery->_id;
					$to_read['value_types'][':a'] = 'int';
					$to_read['condition_types'][':at'] = 'AND';
					$to_read['condition_columns'][':at'] = '_attach_type';
					$to_read['condition_select_types'][':at'] = '=';
					$to_read['condition_values'][':at'] = 'album';
					$to_read['value_types'][':at'] = 'str';
					
					$pics = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
					
					$extra->gallery->pics = $pics;
				
				}
				
				$this->_post->_extra = $extra;
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Retrieve comments associated to the post if they are allowed
			*
			* @access	private
		*/
		
		private function get_comments(){
		
			try{
			
				if($this->_post->_allow_comment == 'open'){
				
					$to_read['table'] = 'comment';
					$to_read['columns'] = array('_id', '_name', '_email', '_content', '_date');
					$to_read['condition_columns'][':ri'] = '_rel_id';
					$to_read['condition_select_types'][':ri'] = '=';
					$to_read['condition_values'][':ri'] = $this->_post->_id;
					$to_read['value_types'][':ri'] = 'int';
					$to_read['condition_types'][':rt'] = 'AND';
					$to_read['condition_columns'][':rt'] = '_rel_type';
					$to_read['condition_select_types'][':rt'] = '=';
					$to_read['condition_values'][':rt'] = 'post';
					$to_read['value_types'][':rt'] = 'str';
					$to_read['condition_types'][':s'] = 'AND';
					$to_read['condition_columns'][':s'] = '_status';
					$to_read['condition_select_types'][':s'] = '=';
					$to_read['condition_values'][':s'] = 'approved';
					$to_read['value_types'][':s'] = 'str';
					$to_read['order'] = array('_date', 'DESC');
					
					$this->_comments = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Comment');
				
				}
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Retrieve related posts via their tags
			*
			* @access	private
		*/
		
		private function get_related_posts(){
		
			try{
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('_title', '_permalink');
				$to_read['condition_columns'][':i'] = '_id';
				$to_read['condition_select_types'][':i'] = '!=';
				$to_read['condition_values'][':i'] = $this->_post->_id;
				$to_read['value_types'][':i'] = 'int';
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array(0, 5);
				
				for($i = 0; $i < count($this->_tags); $i++){
				
					$to_read['condition_types'][':t'.$i] = 'OR';
					$to_read['condition_columns']['group'][':t'.$i] = '_tags';
					$to_read['condition_select_types'][':t'.$i] = 'LIKE';
					$to_read['condition_values'][':t'.$i] = '%'.$this->_tags[$i].'%';
					$to_read['value_types'][':t'.$i] = 'str';
				
				}
				
				$this->_related_posts = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Post');
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Display a post
			*
			* @access	private
		*/
		
		private function display_post(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('post'))
				$tpl::post(
					$this->_post->_title,
					$this->_post->_content,
					$this->_post->_date,
					$this->_post->_user,
					$this->_post->_category,
					$this->_tags,
					$this->_post->_extra,
					$this->_share,
					$this->_post->_permalink
				);
			else
				Html::post(
					$this->_post->_title,
					$this->_post->_content,
					$this->_post->_date,
					$this->_post->_user,
					$this->_post->_category,
					$this->_tags,
					$this->_post->_extra,
					$this->_share,
					$this->_post->_permalink
				);
		
		}
		
		/**
			* Display post comments if they are allowed
			*
			* @access	private
		*/
		
		private function display_comments(){
		
			if($this->_post->_allow_comment == 'open'){
			
				$tpl = $this->_template;
				
				if(Template::_callable('comments'))
					$tpl::comments('o', $this->_post->_id, 'post');
				else
					Html::comments('o', $this->_post->_id, 'post');
					
				if(Template::_callable('comment'))
					foreach($this->_comments as $c)
						$tpl::comment(
							$c->_id,
							$c->_name,
							$c->_email,
							$c->_content,
							$c->_date
						);
				else
					foreach($this->_comments as $c)
						Html::comment(
							$c->_id,
							$c->_name,
							$c->_email,
							$c->_content,
							$c->_date
						);
				
				if(Template::_callable('comments'))
					$tpl::comments('c');
				else
					Html::comments('c');
			
			}
		
		}
		
		/**
			* Display author informations
			*
			* @access	private
		*/
		
		private function display_author(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('author'))
				$tpl::author(
					$this->_post->_user->_publicname,
					$this->_post->_user->_email,
					$this->_post->_user->_website,
					$this->_post->_user->_msn,
					$this->_post->_user->_twitter,
					$this->_post->_user->_facebook,
					$this->_post->_user->_google,
					$this->_post->_user->_bio
				);
			else
				Html::author(
					$this->_post->_user->_publicname,
					$this->_post->_user->_email,
					$this->_post->_user->_website,
					$this->_post->_user->_msn,
					$this->_post->_user->_twitter,
					$this->_post->_user->_facebook,
					$this->_post->_user->_google,
					$this->_post->_user->_bio
				);
		
		}
		
		/**
			* Display related posts
			*
			* @access	private
		*/
		
		private function display_related(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('related'))
				$tpl::related($this->_related_posts);
			else
				Html::related($this->_related_posts);
		
		}
		
		/**
			* Display page menu
			*
			* @access	public
		*/
		
		public function display_menu(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('menu'))
				$tpl::menu($this->_menu);
			else
				Html::menu($this->_menu);
		
		}
		
		/**
			* Display page main content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_post();
			$this->display_comments();
		
		}
		
		/**
			* Display page sidebar
			*
			* @access	public
		*/
		
		public function display_sidebar(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('search'))
				$tpl::search();
			else
				Html::search();
			
			$this->display_author();
			$this->display_related();
		
		}
	
	}

?>