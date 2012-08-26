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
	
	namespace Site\Videos\Controllers;
	use \Site\Master\Controllers\Controller;
	use Exception;
	use \Site\Templates\Helpers\Template;
	use \Admin\Categories\Helpers\Categories;
	use \Site\Master\Helpers\Document;
	use \Library\Database\Database;
	use \Library\Model\User;
	use \Library\Model\Media;
	use \Library\Variable\Get as VGet;
	use \Site\Videos\Html\Category as Html;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Display a list of videos for a specific category
		*
		* @package		Site
		* @subpackage	Videos\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Category extends Controller{
	
		private $_videos = null;
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
			
			$this->get_videos();
			$this->get_categories();
			
			$this->build_title();
			
			Template::publish('videos.category.'.$this->_category);
		
		}
		
		/**
			* Retrieve videos
			*
			* @access	private
		*/
		
		private function get_videos(){
		
			try{
			
				if($this->_category === false)
					throw new Exception('Category id missing');
				
				$to_read['table'] = 'media';
				$to_read['columns'] = array('_id', '_name', '_type', '_user', '_permalink', '_description', '_date', '_attachment', '_attach_type', '_extra');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = 'LIKE';
				$to_read['condition_values'][':t'] = 'video/%';
				$to_read['value_types'][':t'] = 'str';
				$to_read['condition_types'][':s'] = 'AND';
				$to_read['condition_columns'][':s'] = '_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				$to_read['condition_types'][':c'] = 'AND';
				$to_read['condition_columns'][':c'] = '_category';
				$to_read['condition_select_types'][':c'] = 'LIKE';
				$to_read['condition_values'][':c'] = '%"'.$this->_category.'"%';
				$to_read['value_types'][':c'] = 'str';
				
				$this->get_pagination($to_read);
				
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array($this->_limit_start, self::ITEMS_PAGE);
				
				$this->_videos = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
				
				if(!empty($this->_videos))
					foreach($this->_videos as &$v){
					
						$user = new User();
						$user->_id = $v->_user;
						$user->read('_publicname');
						
						$v->_user = $user;
						
						$attachment = $v->_attachment;
						
						if(!empty($attachment) && $v->_attach_type == 'fallback')
							$v->fallback = new Media($v->_attachment);
						else
							$v->fallback = false;
						
						$v->_extra = json_decode($v->_extra);
					
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
			
				$this->_categories = Categories::get_type('video');
			
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
				if($this->_category == $c->_id){
				
					$this->_title = 'Videos > Category "'.$c->_name.'"';
					break;
				
				}
			
			if($this->_page > 1)
				$this->_title .= ' > Page '.$this->_page;
				
		
		}
		
		/**
			* Display videos list
			*
			* @access	private
		*/
		
		private function display_videos(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('videos'))
				$tpl::videos('o');
			else
				Html::videos('o');
			
			if(!empty($this->_videos))
				if(Template::_callable('video'))
					foreach($this->_videos as $v)
						$tpl::video(
							$v->_id,
							$v->_name,
							$v->_type,
							$v->_user,
							$v->_permalink,
							$v->_description,
							$v->_date,
							$v->_extra,
							$v->fallback
						);
				else
					foreach($this->_videos as $v)
						Html::video(
							$v->_id,
							$v->_name,
							$v->_type,
							$v->_user,
							$v->_permalink,
							$v->_description,
							$v->_date,
							$v->_extra,
							$v->fallback
						);
			else
				if(Template::_callable('no_data'))
					$tpl::no_data();
				else
					Html::no_data();
			
			if(Template::_callable('videos'))
				$tpl::videos('c');
			else
				Html::videos('c');
		
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
		
			$this->display_videos();
			
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