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
	use \Library\Database\Database;
	use \Library\Model\User;
	use \Site\Posts\Html\Category as Html;
	use \Site\Posts\Helpers\Post as HPost;
	use \Library\Model\Category as MCategory;
	use \Library\Model\Media;
	use \Site\Master\Helpers\Document;
	use \Admin\Categories\Helpers\Categories;
	use \Library\Variable\Get as VGet;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Controllers that display the list of published posts in a specific category
		*
		* @package		Site
		* @subpackage	Posts\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Category extends Controller{
	
		private $_posts = null;
		private $_category = null;
		private $_categories = null;
		private $_page = null;
		private $_limit_start = null;
		private $_max = null;
		const ITEMS_PAGE = 10;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_category = VGet::id(false);
			
			$this->get_posts();
			$this->get_categories();
			
			$this->build_title();
			
			Template::publish('posts.category.'.$this->_category);
		
		}
		
		/**
			* Retrieve published posts
			*
			* @access	private
		*/
		
		private function get_posts(){
		
			try{
			
				if($this->_category === false)
					throw new Exception('Category id missing');
				
				$to_read['table'] = 'post';
				$to_read['columns'] = array('_id', '_title', '_content', '_date', '_user', '_category', '_permalink', '_extra');
				$to_read['condition_columns'][':s'] = '_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				$to_read['condition_types'][':c'] = 'AND';
				$to_read['condition_columns'][':c'] = '_category';
				$to_read['condition_select_types'][':c'] = 'LIKE';
				$to_read['condition_values'][':c'] = '%"'.$this->_category.'"%';
				$to_read['value_types'][':c'] = 'int';
				
				$this->get_pagination($to_read);
				
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array($this->_limit_start, self::ITEMS_PAGE);
				
				$this->_posts = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Post');
				
				if(!empty($this->_posts))
					foreach($this->_posts as &$p){
					
						$user = new User();
						$user->_id = $p->_user;
						$user->read('_publicname');
						
						$p->_user = $user;
						
						$p->_content = HPost::trim($p->_content);
						
						$categories = json_decode($p->_category, true);
						
						foreach($categories as &$c)
							$c = new MCategory($c);
						
						$p->_category = $categories;
						
						$extra = json_decode($p->_extra);
						
						if(!empty($extra->banner))
							$extra->banner = new Media($extra->banner);
						
						$p->_extra = $extra;
					
					}
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Get pagination informations
			*
			* @private
			* @param	array	[$to_read]
		*/
		
		private function get_pagination($to_read){
		
			try{
			
				list($this->_page, $this->_limit_start) = Document::pagination(self::ITEMS_PAGE);
				
				$to_read['columns'] = array('COUNT(_id) as count');
				
				$count = $this->_db->read($to_read);
				
				$this->_max = ceil($count[0]['count']/self::ITEMS_PAGE);
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Retrieve posts categories
			*
			* @access	private
		*/
		
		private function get_categories(){
		
			try{
			
				$this->_categories = Categories::get_type('post');
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Build page title
			*
			* @access	private
		*/
		
		private function build_title(){
		
			foreach($this->_categories as $c)
				if($c->_id == $this->_category){
				
					$this->_title = 'Posts > Category "'.$c->_name.'"';
					break;
				
				}
			
			if(!empty($this->_title) && $this->_page > 1)
				$this->_title .= ' > Page '.$this->_page;
			
			if(empty($this->_title))
				$this->_title = 'Posts';
		
		}
		
		/**
			* Display the posts list
			*
			* @access	private
		*/
		
		private function display_posts(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('posts'))
				$tpl::posts('o');
			else
				Html::posts('o');
			
			if(!empty($this->_posts))
				if(Template::_callable('post'))
					foreach($this->_posts as $p)
						$tpl::post(
							$p->_title,
							$p->_content,
							$p->_date,
							$p->_user,
							$p->_category,
							$p->_permalink,
							$p->_extra->banner
						);
				else
					foreach($this->_posts as $p)
						Html::post(
							$p->_title,
							$p->_content,
							$p->_date,
							$p->_user,
							$p->_category,
							$p->_permalink,
							$p->_extra->banner
						);
			else
				if(Template::_callable('no_data'))
					$tpl::no_data();
				else
					Html::no_data();
			
			if(Template::_callable('posts'))
				$tpl::posts('c');
			else
				Html::posts('c');
		
		}
		
		/**
			* Display pagination links
			*
			* @access	private
		*/
		
		private function display_pagination(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('pagination'))
				$tpl::pagination($this->_page, $this->_max);
			else
				Html::pagination($this->_page, $this->_max);
		
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
		
			$this->display_posts();
			
			$this->display_pagination();
		
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
				
			if(Template::_callable('categories'))
				$tpl::categories($this->_categories);
			else
				Html::categories($this->_categories);
		
		}
	
	}

?>