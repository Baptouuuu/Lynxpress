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
	use \Library\Lang\Lang;
	use \Admin\Media\Html\Manage as Html;
	use \Admin\ActionMessages\ActionMessages;
	use Exception;
	use \Library\Variable\Get as VGet;
	use \Library\Variable\Post as VPost;
	use \Library\Variable\Request as VRequest;
	use \Library\Database\Database;
	use \Admin\Categories\Helpers\Categories;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Model\User;
	use \Library\Model\Media;
	use \Library\Media\Media as HMedia;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Manage controllers allows to view uploaded medias
		*
		* @package		Admin
		* @subpackage	Media\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master implements Controller{
	
		private $_medias = null;
		private $_type = null;
		private $_types = null;
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
			
			$this->_title = Lang::_('Media');
			
			if($this->_user->_permissions->media){
			
				if(in_array(VRequest::type('image'), array('image', 'video', 'alien')))
					$this->_type = VRequest::type('image');
				else
					$this->_type = 'image';
				
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.table.js');
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.button_confirm.js');
				
				$this->delete();
				
				$this->get_medias();
				$this->get_types();
				
				if($this->_type == 'video')
					$this->get_categories();
			
			}
		
		}
		
		/**
			* Retrieve medias for the specified type from database
			*
			* @access	private
		*/
		
		private function get_medias(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('_id', '_name', '_type', '_user', '_permalink', '_embed_code', '_date');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = 'LIKE';
				$to_read['condition_values'][':t'] = $this->_type.'%';
				$to_read['value_types'][':t'] = 'str';
				
				if((VPost::search_button() && VPost::search()) || VGet::search()){
				
					$search = '%'.trim(VRequest::search()).'%';
					
					$to_read['condition_columns']['group'][':n'] = '_name';
					$to_read['condition_select_types'][':n'] = 'LIKE';
					$to_read['condition_values'][':n'] = $search;
					$to_read['value_types'][':n'] = 'str';
					$to_read['condition_types'][':d'] = 'OR';
					$to_read['condition_columns']['group'][':d'] = '_description';
					$to_read['condition_select_types'][':d'] = 'LIKE';
					$to_read['condition_values'][':d'] = $search;
					$to_read['value_types'][':d'] = 'str';
				
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
				
				}elseif(VGet::user()){
				
					$to_read['condition_types'][':u'] = 'AND';
					$to_read['condition_columns'][':u'] = '_user';
					$to_read['condition_select_types'][':u'] = '=';
					$to_read['condition_values'][':u'] = VGet::user();
					$to_read['value_types'][':u'] = 'int';
				
				}
				
				if($this->_type == 'image'){
				
					$to_read['condition_types'][':atype'] = 'AND';
					$to_read['condition_columns'][':atype'] = '_attach_type';
					$to_read['condition_select_types'][':atype'] = '=';
					$to_read['condition_values'][':atype'] = 'none';
					$to_read['value_types'][':atype'] = 'str';
				
				}
				
				//pass $to_read by parameter to have same conditions
				$this->get_pagination($to_read);
				$this->get_dates($to_read);
				
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array($this->_limit_start, self::ITEMS);
				
				$this->_medias = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Media');
				
				if(!empty($this->_medias))
					foreach($this->_medias as &$m){
					
						$u = new User();
						$u->_id = $m->_user;
						$u->read('_username');
						
						$m->_ouser = $u;
					
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
			* Retrieve numbers count for each media type
			*
			* @access	private
		*/
		
		private function get_types(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('substr(_type, 1, 5) as _type', 'count(substr(_type, 1, 5)) as count');
				$to_read['condition_columns'][':at'] = '_attach_type';
				$to_read['condition_select_types'][':at'] = '!=';
				$to_read['condition_values'][':at'] = 'album';
				$to_read['value_types'][':at'] = 'str';
				$to_read['groupby'] = 'substr(_type, 1, 5)';
				
				$types = $this->_db->read($to_read);
				
				$ts = array('image' => 0, 'video' => 0, 'alien' => 0);
				
				foreach($ts as $key => &$count)
					if(!empty($types))
						foreach($types as $t)
							if($t['_type'] == $key)
								$count = $t['count'];
				
				$this->_types = $ts;
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve dates for retrieved medias
			*
			* @access	private
			* @param	array	[$tp_read] Array from get_medias
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
			* Retrieve video categories
			*
			* @access	private
		*/
		
		private function get_categories(){
		
			$this->_categories = Categories::get_type('video');
		
		}
		
		/**
			* Display page menu
			*
			* @access	private
		*/
		
		private function display_menu(){
		
			Html::menu($this->_user->_permissions->album);
		
		}
		
		/**
			* Display submenu and actions
			*
			* @access	private
		*/
		
		private function display_actions(){
		
			Html::submenu('o');
			
			$types = array('image' => Lang::_('Image'), 'video' => Lang::_('Video'), 'alien' => Lang::_('External Video'));
			
			foreach($types as $t => $d){
			
				$selected = false;
				
				if($t == $this->_type)
					$selected = true;
				
				Html::submenu_type($t, $d, $this->_types[$t], $selected);
			
			}
			
			Html::submenu('c');
			
			Html::actions('o');
			
			if(!empty($this->_dates))
				foreach($this->_dates as $d)
					Html::option($d['_date'], date('F Y', strtotime($d['_date'])));
			
			if($this->_type == 'video'){
			
				Html::actions('m');
				
				if(!empty($this->_categories))
					foreach($this->_categories as $c)
						Html::option($c->_id, $c->_name);
			
			}
			
			Html::actions('c', $this->_type);
		
		}
		
		/**
			* Display medias table
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::table('o');
			
			if(!empty($this->_medias))
				foreach($this->_medias as $m)
					Html::media(
						$m->_id,
						$m->_name,
						$m->_type,
						$m->_ouser,
						$m->_permalink,
						$m->_embed_code,
						$m->_date
					);
			else
				Html::no_media();
			
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
					$link = array('filter' => 'true', 'date' => VRequest::date(), 'category' => VRequest::category(), 'type' => $this->_type);
				elseif(VRequest::search())
					$link = array('search' => trim(VRequest::search()), 'type' => $this->_type);
				elseif(VGet::user())
					$link = array('user' => VGet::user(), 'type' => $this->_type);
				else
					$link = array('type' => $this->_type);
				
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
			
			if($this->_user->_permissions->media){
			
				echo $this->_action_msg;
				
				Html::form('o', 'post', Url::_(array('ns' => 'media'), array('type' => $this->_type)));
				
				$this->display_actions();
				
				$this->display_table();
				
				$this->display_pagination();
				
				//create a datalist for search input
				echo Helper::datalist('titles', $this->_medias, '_name');
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Delete medias from database and from hard disk
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VGet::action() == 'delete' && VGet::id() && $this->_user->_permissions->delete){
			
				try{
				
					$m = new Media();
					$m->_id = VGet::id();
					$m->read('_permalink');
					$m->read('_name');
					
					if(!HMedia::delete(PATH.$m->_permalink))
						throw new Exception(Lang::_('File "%file" not deleted', 'master', array('file' => $m->_permalink)));
					
					$m->delete();
					
					Activity::log('deleted the file "'.$m->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VPost::delete(false) && VPost::media_id() && $this->_user->_permissions->media){
			
				try{
				
					foreach(VPost::media_id() as $id){
					
						$m = new Media();
						$m->_id = $id;
						$m->read('_permalink');
						$m->read('_name');
						
						if(!HMedia::delete(PATH.$m->_permalink))
							throw new Exception(Lang::_('File "%file" not deleted', 'master', array('file' => $m->_permalink)));
						
						$m->delete();
						
						Activity::log('deleted the file "'.$m->_name.'"');
					
					}
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(((VGet::action() == 'delete' && VGet::id()) || (VPost::delete(false) && VPost::media_id())) && !$this->_user->_permissions->delete){
			
				$this->_action_msg .= ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>