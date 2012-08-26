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
	
	namespace Admin\Categories\Controllers;
	use \Admin\Master\Controllers\Controller;
	use \Library\Lang\Lang as Lang;
	use \Admin\Categories\Html\Manage as Html;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Database\Database as Database;
	use Exception;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Model\Category as Category;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Admin\Activity\Helpers\Activity;
	use \Admin\Categories\Helpers\Categories;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows user to create and delete categories
		*
		* @package		Admin
		* @subpackage	Categories\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Controller{
	
		private $_categories = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Categories';
			
			if($this->_user->_permissions->setting){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.table.js');
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.button_confirm.js');
			
				$this->create();
				$this->delete();
				
				$this->get_categories();
			
			}
		
		}
		
		/**
			* Retrieve all categories
			*
			* @access	private
		*/
		
		private function get_categories(){
		
			try{
			
				$to_read['table'] = 'category';
				$to_read['columns'] = array('*');
				$to_read['order'] = array('_type', 'ASC');
				
				$this->_categories = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Category');
			
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
		
			Html::menu();
		
		}
		
		/**
			* Display categories with a form to create one
			*
			* @access	private
		*/
		
		private function display_categories(){
		
			Html::actions();
			
			Html::table('o');
			
			if(!empty($this->_categories))
				foreach($this->_categories as $c)
					Html::category(
						$c->_id,
						$c->_name,
						$c->_type
					);
			
			Html::table('c');
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			Html::noscript();
			
			if($this->_user->_permissions->setting){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'categories')));
				
				$this->display_categories();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Create a new category
			*
			* @access	private
		*/
		
		private function create(){
		
			if(VPost::create() && VPost::new_category()){
			
				try{
				
					$to_read['table'] = 'category';
					$to_read['columns'] = array('_id');
					$to_read['condition_columns'][':n'] = '_name';
					$to_read['condition_select_types'][':n'] = 'LIKE';
					$to_read['condition_values'][':n'] = VPost::new_category();
					$to_read['value_types'][':n'] = 'str';
					$to_read['condition_types'][':t'] = 'AND';
					$to_read['condition_columns'][':t'] = '_type';
					$to_read['condition_select_types'][':t'] = '=';
					$to_read['condition_values'][':t'] = VPost::type();
					$to_read['value_types'][':t'] = 'str';
					
					$cats = $this->_db->read($to_read);
					
					if(!empty($cats))
						throw new Exception(Lang::_('Category already existing', 'categories'));
				
					$cat = new Category();
					$cat->_name = VPost::new_category();
					$cat->_type = VPost::type();
					$cat->create();
					
					Activity::log('created the category "'.$cat->_name.'" ('.$cat->_type.')');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::created($result);
			
			}
		
		}
		
		/**
			* Delete categories with a use check before
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VGet::action() == 'delete' && VGet::id() && $this->_user->_permissions->delete){
			
				try{
				
					$cat = new Category(VGet::id());
					
					if($cat->_type == 'post')
						$to_read['table'] = 'post';
					elseif($cat->_type == 'video' || $cat->_type == 'album')
						$to_read['table'] = 'media';
					
					$to_read['columns'] = array('_id');
					$to_read['condition_columns'][':c'] = '_category';
					$to_read['condition_select_types'][':c'] = 'LIKE';
					$to_read['condition_values'][':c'] = '%"'.$cat->_id.'"%';
					$to_read['value_types'][':c'] = 'str';
					
					$content = $this->_db->read($to_read);
					
					if(!empty($content))
						throw new Exception(Lang::_('Category "%a" is used', 'categories', array('a' => ucfirst($cat->_name))));
					
					$cat->delete();
					
					Activity::log('deleted the category "'.$cat->_name.'"');
					
					$count = Categories::get_type($cat->_type);
					
					if(count($count) == 0){
					
						$new = new Category();
						$new->_name = 'Uncategorized';
						$new->_type = $cat->_type;
						$new->create();
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VPost::delete() && VPost::category_id() && $this->_user->_permissions->delete){
			
				try{
				
					foreach(VPost::category_id() as $id){
					
						$cat = new Category($id);
						
						if($cat->_type == 'post')
							$to_read['table'] = 'post';
						elseif($cat->_type == 'video' || $cat->_type == 'album')
							$to_read['table'] = 'media';
						
						$to_read['columns'] = array('_id');
						$to_read['condition_columns'][':c'] = '_category';
						$to_read['condition_select_types'][':c'] = 'LIKE';
						$to_read['condition_values'][':c'] = '%"'.$cat->_id.'"%';
						$to_read['value_types'][':c'] = 'str';
						
						$content = $this->_db->read($to_read);
						
						if(!empty($content))
							throw new Exception(Lang::_('Category "%a" is used', 'categories', array('a' => ucfirst($cat->_name))));
						
						$cat->delete();
						
						Activity::log('deleted the category "'.$cat->_name.'"');
						
						$count = Categories::get_type($cat->_type);
						
						if(count($count) == 0){
						
							$new = new Category();
							$new->_name = 'Uncategorized';
							$new->_type = $cat->_type;
							$new->create();
						
						}
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}
		
		}
	
	}

?>