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
	
	namespace Admin\Media\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Admin\Media\Html\Albums as Html;
	use \Library\Lang\Lang;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Database\Database;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Request as VRequest;
	use \Admin\Categories\Helpers\Categories;
	use \Library\Model\User;
	use \Library\Model\Category;
	use \Library\Model\Media;
	use \Library\Media\Media as HMedia;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Albums controller allow to edit picture albums
		*
		* @package		Admin
		* @subpackage	Media\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Albums extends Master implements Controller{
	
		private $_albums = null;
		private $_dates = null;
		private $_categories = null;
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
			
			$this->_title = Lang::_('Album');
			
			if($this->_user->_permissions->album){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/labels.js');
			
				$this->update();
				$this->delete();
				
				$this->get_albums();
				$this->get_categories();
			
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
				$to_read['columns'] = array('_id', '_name', '_user', '_status', '_category', '_permalink', '_date');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'album';
				$to_read['value_types'][':t'] = 'str';
				
				if((VPost::search_button() && VPost::search()) || VGet::search()){
				
					$to_read['condition_types'][':n'] = 'AND';
					$to_read['condition_columns'][':n'] = '_name';
					$to_read['condition_select_types'][':n'] = 'LIKE';
					$to_read['condition_values'][':n'] = '%'.VRequest::search().'%';
				
				}elseif(VRequest::filter(false) && (VRequest::date('all') !== 'all' || VRequest::category('all') !== 'all')){
				
					if(VRequest::date('all') !== 'all'){
					
						$to_read['condition_types'][':d'] = 'AND';
						$to_read['condition_columns'][':d'] = '_date';
						$to_read['condition_select_types'][':d'] = 'LIKE';
						$to_read['condition_values'][':d'] = VRequest::date().'%';
						$to_read['value_types'][':d'] = 'str';
					
					}
					
					if(VRequest::category('all') !== 'all'){
					
						$to_read['condition_types'][':c'] = 'AND';
						$to_read['condition_columns'][':c'] = '_category';
						$to_read['condition_select_types'][':c'] = 'LIKE';
						$to_read['condition_values'][':c'] = '%'.VRequest::category().'%';
						$to_read['value_types'][':c'] = 'str';
					
					}
				
				}
				
				//pass $to_read by parameter to have same conditions
				$this->get_pagination($to_read);
				$this->get_dates($to_read);
				
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array($this->_limit_start, self::ITEMS);
				
				$this->_albums = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
				
				if(!empty($this->_albums))
					foreach($this->_albums as &$a){
					
						$u = new User($a->_user);
						$a->_ouser = $u;
						
						$ids = explode(',', $a->_category);
						$cats = array();
						
						foreach($ids as $i){
						
							$c = new Category($i);
							$cats[] = $c->_name;
						
						}
						
						$a->_category = implode(', ', $cats);
					
					}
			
			}catch(Exception $e){
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
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
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve dates for retrieved albums
			*
			* @access	private
			* @param	array	[$tp_read] Array from get_albums
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
			
				$this->_action_msg = ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve albums categories
			*
			* @access	private
		*/
		
		private function get_categories(){
		
			$this->_categories = Categories::get_type('album');
		
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
			* Display albums actions
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			Html::actions('o');
			
			if(!empty($this->_dates))
				foreach($this->_dates as $d)
					Html::option($d['_date'], date('F Y', strtotime($d['_date'])));
			
			Html::actions('m');
			
			if(!empty($this->_categories))
				foreach($this->_categories as $c)
					Html::option($c->_id, $c->_name);
			
			Html::actions('c');
		
		}
		
		/**
			* Display albums labels
			*
			* @access	private
		*/
		
		private function display_labels(){
		
			Html::albums('o');
			
			if(!empty($this->_albums))
				foreach($this->_albums as $a)
					Html::album(
						$a->_id,
						$a->_name,
						$a->_ouser,
						$a->_status,
						$a->_category,
						$a->_permalink,
						$a->_date
					);
			else
				Html::no_album();
			
			Html::albums('c');
		
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
					$link = array('filter' => 'true', 'date' => VRequest::date(), 'category' => VRequest::category());
				elseif(VRequest::search())
					$link = array('search' => trim(VRequest::search()));
				
				Html::pagination($this->_page, $this->_max, $link);
			
			}
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_menu();
			
			Html::noscript();
			
			if($this->_user->_permissions->album){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'media', 'ctl' => 'albums')));
				
				$this->display_actions();
				
				$this->display_labels();
				
				$this->display_pagination();
				
				//create a datalist for search input
				echo Helper::datalist('titles', $this->_albums, '_name');
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Update status of albums
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::apply() && in_array(VPost::action(), array('publish', 'unpublish')) && VPost::album_id()){
			
				try{
				
					if(VPost::action() == 'publish')
						$status = 'publish';
					elseif(VPost::action() == 'unpublish')
						$status = 'draft';
				
					foreach(VPost::album_id() as $id){
					
						$a = new Media();
						$a->_id = $id;
						$a->_status = $status;
						$a->update('_status');
						$a->read('_name');
						
						Activity::log('updated the album "'.$a->_name.'" (status: '.$a->_status.')');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::updated($result);
			
			}
		
		}
		
		/**
			* Delete albums and associated pictures
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::apply() && VPost::action() == 'delete' && VPost::album_id() && $this->_user->_permissions->delete){
			
				try{
				
					$to_read['table'] = 'media';
					$to_read['columns'] = array('_id', '_permalink');
					$to_read['condition_columns'][':a'] = '_attachment';
					$to_read['condition_select_types'][':a'] = '=';
					$to_read['value_types'][':a'] = 'int';
					$to_read['condition_types'][':at'] = 'AND';
					$to_read['condition_columns'][':at'] = '_attach_type';
					$to_read['condition_select_types'][':at'] = '=';
					$to_read['condition_values'][':at'] = 'album';
					$to_read['value_types'][':at'] = 'str';
					
					foreach(VPost::album_id() as $aid){
					
						$to_read['condition_values'][':a'] = $aid;
						
						$pics = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
						
						if(!empty($pics))
							foreach($pics as $p){
							
								HMedia::delete(PATH.$p->_permalink);
								$p->delete();
							
							}
						
						$album = new Media();
						$album->_id = $aid;
						$album->read('_name');
						$album->read('_permalink');
						HMedia::delete(PATH.$album->_permalink.'cover.png');
						$album->delete();
						@rmdir(PATH.$album->_permalink);
						
						$this->_db->query('DELETE FROM `'.DB_PREFIX.'comment` WHERE _rel_id = '.$aid.' AND _rel_type = "media"');
						
						Activity::log('deleted the album "'.$album->_name.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg = ActionMessages::deleted($result);
			
			}elseif(VPost::apply() && VPost::action() == 'delete' && VPost::album_id() && !$this->_user->_permissions->delete){
			
				$this->_action_msg = ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>