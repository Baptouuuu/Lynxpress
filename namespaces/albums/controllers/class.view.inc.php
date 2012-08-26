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
	use \Library\Variable\Get as VGet;
	use \Library\Model\Media;
	use \Library\Model\User;
	use \Library\Model\Category;
	use \Library\Database\Database;
	use \Site\Albums\Html\View as Html;
	use \Site\Master\Helpers\Document;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Display a specific album with its comments
		*
		* @package		Site
		* @subpackage	Albums\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class View extends Controller{
	
		private $_album = null;
		private $_pictures = null;
		private $_comments = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->get_album();
			$this->get_pictures();
			$this->get_comments();
			
			$this->_title = $this->_album->_name;
			
			Template::publish('albums.view.'.$this->_album->_id.(($this->_album->_allow_comment == 'open')?'.comments':''));
		
		}
		
		/**
			* Retrieve album informations
			*
			* @access	private
		*/
		
		private function get_album(){
		
			try{
			
				if(!VGet::id())
					throw new Exception('album id missing');
				
				$this->_album = new Media(VGet::id());
				
				$user = new User();
				$user->_id = $this->_album->_user;
				$user->read('_publicname');
				$user->read('_email');
				$user->read('_website');
				$user->read('_msn');
				$user->read('_twitter');
				$user->read('_facebook');
				$user->read('_google');
				$user->read('_bio');
				
				$this->_album->_user = $user;
				
				$cats = json_decode($this->_album->_category, true);
				
				foreach($cats as &$c)
					$c = new Category($c);
				
				$this->_album->_category = $cats;
				
				$this->_album->_extra = json_decode($this->_album->_extra);
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Retrieve album pictures
			*
			* @access	private
		*/
		
		private function get_pictures(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('_name', '_permalink', '_description', '_date', '_extra');
				$to_read['condition_columns'][':a'] = '_attachment';
				$to_read['condition_select_types'][':a'] = '=';
				$to_read['condition_values'][':a'] = $this->_album->_id;
				$to_read['value_types'][':a'] = 'int';
				$to_read['condition_types'][':at'] = 'AND';
				$to_read['condition_columns'][':at'] = '_attach_type';
				$to_read['condition_select_types'][':at'] = '=';
				$to_read['condition_values'][':at'] = 'album';
				$to_read['value_types'][':at'] = 'str';
				$to_read['order'] = array('_name', 'ASC');
				
				$this->_pictures = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
				
				if(!empty($this->_pictures))
					foreach($this->_pictures as &$p)
						$p->_extra = json_decode($p->_extra);
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Retrieve comments associated to the album if they are allowed
			*
			* @access	private
		*/
		
		private function get_comments(){
		
			try{
			
				if($this->_album->_allow_comment == 'open'){
				
					$to_read['table'] = 'comment';
					$to_read['columns'] = array('_id', '_name', '_email', '_content', '_date');
					$to_read['condition_columns'][':ri'] = '_rel_id';
					$to_read['condition_select_types'][':ri'] = '=';
					$to_read['condition_values'][':ri'] = $this->_album->_id;
					$to_read['value_types'][':ri'] = 'int';
					$to_read['condition_types'][':rt'] = 'AND';
					$to_read['condition_columns'][':rt'] = '_rel_type';
					$to_read['condition_select_types'][':rt'] = '=';
					$to_read['condition_values'][':rt'] = 'media';
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
			* Display album with its pictures
			*
			* @access	private
		*/
		
		private function display_album(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('album'))
				$tpl::album(
					$this->_album->_name,
					$this->_album->_date,
					$this->_album->_category,
					$this->_album->_description,
					$this->_album->_extra,
					$this->_share,
					$this->_album->_id
				);
			else
				Html::album(
					$this->_album->_name,
					$this->_album->_date,
					$this->_album->_category,
					$this->_album->_description,
					$this->_album->_extra,
					$this->_share,
					$this->_album->_id
				);
			
			if(Template::_callable('pictures'))
				$tpl::pictures('o');
			else
				Html::pictures('o');
			
			if(!empty($this->_pictures))
				if(Template::_callable('picture'))
					foreach($this->_pictures as $p)
						$tpl::picture(
							$p->_name,
							$p->_permalink,
							$p->_description,
							$p->_date,
							$p->_extra
						);
				else
					foreach($this->_pictures as $p)
						Html::picture(
							$p->_name,
							$p->_permalink,
							$p->_description,
							$p->_date,
							$p->_extra
						);
			
			if(Template::_callable('pictures'))
				$tpl::pictures('c');
			else
				Html::pictures('c');
		
		}
		
		/**
			* Display post comments if they are allowed
			*
			* @access	private
		*/
		
		private function display_comments(){
		
			if($this->_album->_allow_comment == 'open'){
			
				$tpl = $this->_template;
				
				if(Template::_callable('comments'))
					$tpl::comments('o', $this->_album->_id, 'media');
				else
					Html::comments('o', $this->_album->_id, 'media');
					
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
					$this->_album->_user->_publicname,
					$this->_album->_user->_email,
					$this->_album->_user->_website,
					$this->_album->_user->_msn,
					$this->_album->_user->_twitter,
					$this->_album->_user->_facebook,
					$this->_album->_user->_google,
					$this->_album->_user->_bio
				);
			else
				Html::author(
					$this->_album->_user->_publicname,
					$this->_album->_user->_email,
					$this->_album->_user->_website,
					$this->_album->_user->_msn,
					$this->_album->_user->_twitter,
					$this->_album->_user->_facebook,
					$this->_album->_user->_google,
					$this->_album->_user->_bio
				);
		
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
		
			$this->display_album();
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
		
		}
	
	}

?>