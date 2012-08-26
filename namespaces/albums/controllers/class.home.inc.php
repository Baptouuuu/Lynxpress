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
	
	namespace Site\Albums\Controllers;
	use \Site\Master\Controllers\Controller;
	use \Site\Templates\Helpers\Template;
	use Exception;
	use \Site\Master\Helpers\Document;
	use \Library\Database\Database;
	use \Admin\Categories\Helpers\Categories;
	use \Library\Model\User;
	use \Library\Model\Category;
	use \Site\Albums\Html\Home as Html;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Display a list of published albums
		*
		* @package		Site
		* @subpackage	Albums\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Home extends Controller{
	
		private $_albums = null;
		private $_categories = null;
		private $_page = null;
		private $_limit_start = null;
		private $_max = null;
		const ITEMS_PAGE = 42;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->get_albums();
			$this->get_categories();
			
			$this->build_title();
			
			Template::publish('albums');
		
		}
		
		/**
			* Retrieve published albums
			*
			* @access	private
		*/
		
		private function get_albums(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('_id', '_name', '_user', '_category', '_permalink', '_date');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'album';
				$to_read['value_types'][':t'] = 'str';
				$to_read['condition_types'][':s'] = 'AND';
				$to_read['condition_columns'][':s'] = '_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				
				$this->get_pagination($to_read);
				
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array($this->_limit_start, self::ITEMS_PAGE);
				
				$this->_albums = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
				
				if(!empty($this->_albums))
					foreach($this->_albums as &$a){
					
						$user = new User();
						$user->_id = $a->_user;
						$user->read('_publicname');
						
						$a->_user = $user;
						
						$categories = json_decode($a->_category, true);
						
						foreach($categories as &$c)
							$c = new Category($c);
						
						$a->_category = $categories;
					
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
			
				$this->_categories = Categories::get_type('album');
			
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
		
			$this->_title = 'Albums';
			
			if($this->_page > 1)
				$this->_title .= ' > Page '.$this->_page;
		
		}
		
		/**
			* Display albums list
			*
			* @access	private
		*/
		
		private function display_albums(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('albums'))
				$tpl::albums('o');
			else
				Html::albums('o');
			
			if(!empty($this->_albums))
				if(Template::_callable('album'))
					foreach($this->_albums as $a)
						$tpl::album(
							$a->_id,
							$a->_name,
							$a->_user,
							$a->_category,
							$a->_permalink,
							$a->_date
						);
				else
					foreach($this->_albums as $a)
						Html::album(
							$a->_id,
							$a->_name,
							$a->_user,
							$a->_category,
							$a->_permalink,
							$a->_date
						);
			else
				if(Template::_callable('no_data'))
					$tpl::no_data();
				else
					Html::no_data();
			
			if(Template::_callable('albums'))
				$tpl::albums('c');
			else
				Html::albums('c');
		
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
		
			$this->display_albums();
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