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
	
	namespace Admin\Activity\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller as Controller;
	use \Library\Lang\Lang as Lang;
	use \Admin\Activity\Html\Manage as Html;
	use \Admin\ActionMessages\ActionMessages as ActionMessages;
	use \Library\Database\Database as Database;
	use Exception;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Model\User as User;
	use \Library\Variable\Post as VPost;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allow user to see website backend activity and reset it
		*
		* @package		Admin
		* @subpackage	Activity\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master implements Controller{
	
		private $_activity = null;
		const ITEMS = 42;
		private $_page = null;
		private $_limit_start = null;
		private $_max = null;
		
		/**
			* Class contructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Activity');
			
			if($this->_user->_permissions->setting){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.button_confirm.js');
			
				$this->delete();
			
				$this->get_activity();
			
			}
		
		}
		
		/**
			* Retrieve activity messages
			*
			* @access	private
		*/
		
		private function get_activity(){
		
			try{
			
				$to_read['table'] = 'activity';
				$to_read['columns'] = array('*');
				
				//pass $to_read by parameter to have same conditions
				$this->get_pagination($to_read);
				
				$to_read['limit'] = array($this->_limit_start, self::ITEMS);
				$to_read['order'] = array('_date', 'DESC');
				
				$this->_activity = $this->_db->read($to_read, Database::FETCH_OBJ);
				
				if(!empty($this->_activity))
					foreach($this->_activity as &$a)
						$a->_user = new User($a->user_id);
			
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
				
				$to_read['columns'] = array('COUNT(_data) as count');
				
				$count = $this->_db->read($to_read);
				
				$this->_max = ceil($count[0]['count']/self::ITEMS);
			
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
			* Display activity table
			*
			* @access	private
		*/
		
		private function display_table(){
		
			Html::table('o');
			
			if(!empty($this->_activity))
				foreach($this->_activity as $a)
					Html::activity(
						$a->_user->_username,
						$a->_user->_email,
						$a->_data,
						$a->_date
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
				
				Html::form('o', 'post', Url::_(array('ns' => 'activity')));
				
				$this->display_table();
				
				if($this->_max > 1)
					Html::pagination($this->_page, $this->_max);
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Delete activity and insert who deleted it
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete() && $this->_user->_permissions->delete){
			
				try{
				
					$this->_db->query('TRUNCATE TABLE '.DB_PREFIX.'activity');
					
					Activity::log('deleted website backend activity');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::deleted($result);
			
			}elseif(VPost::delete() && !$this->_user->_permissions->delete){
			
				$this->_action_msg .= ActionMessages::action_no_perm();
			
			}
		
		}
	
	}

?>