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
	
	namespace Admin\Plugins\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang;
	use \Admin\Plugins\Html\Manage as Html;
	use \Admin\ActionMessages\ActionMessages;
	use \Library\Database\Database;
	use \Library\Url\Url;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Variable\Post as VPost;
	use \Library\Model\Setting;
	use \Admin\Activity\Helpers\Activity;
	use \Library\File\File;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows user to manage plugins installed on the website
		*
		* @package		Admin
		* @subpackage	Plugins\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master implements Controller{
	
		private $_plugins = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Plugins');
			
			if($this->_user->_permissions->setting){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.labels.js');
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.button_confirm.js');
				
				$this->delete();
				
				$this->get_plugins();
			
			}
		
		}
		
		/**
			* Retrieve installed plugins informations
			*
			* @access	private
		*/
		
		private function get_plugins(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('*');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'plugin';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_plugins = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Setting');
				
				if(!empty($this->_plugins))
					foreach($this->_plugins as &$p)
						$p->_data = json_decode($p->_data);
			
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
			* Display plugins list
			*
			* @access	private
		*/
		
		private function display_plugins(){
		
			Html::actions();
			
			Html::plugins('o');
			
			if(!empty($this->_plugins))
				foreach($this->_plugins as $p)
					Html::plugin(
						$p->_id,
						$p->_name,
						$p->_data->author,
						$p->_data->url
					);
			else
				Html::no_plugins();
			
			Html::plugins('c');
		
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
				
				Html::form('o', 'post', Url::_(array('ns' => 'plugins')));
				
				$this->display_plugins();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Delete a plugin from database and remove all files
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete() && VPost::plugin_id() && $this->_user->_permissions->delete){
			
				try{
				
					foreach(VPost::plugin_id() as $id){
					
						$plg = new Setting($id);
						
						$manifest = json_decode($plg->_data);
						$ns = $manifest->infos->namespace;
						
						if($manifest->uninstall === true){
						
							$class = '\\Admin\\'.$ns.'\\Uninstall';
							new $class();
							
							File::delete('namespaces/'.$ns.'/class.uninstall.inc.php');
						
						}
						
						foreach($manifest->site->core as $f)
							File::delete(PATH.'namespaces/'.$ns.'/'.$f, false);
						
						foreach($manifest->site->css as $f)
							File::delete(PATH.'css/site/'.$ns.'/'.$f, false);
						
						foreach($manifest->site->js as $f)
							File::delete(PATH.'js/site/'.$ns.'/'.$f, false);
						
						foreach($manifest->images as $f)
							File::delete(PATH.'images/'.$ns.'/'.$f, false);
						
						foreach($manifest->admin->core as $f)
							File::delete('namespaces/'.$ns.'/'.$f, false);
						
						foreach($manifest->admin->css as $f)
							File::delete(PATH.'css/admin/'.$ns.'/'.$f, false);
						
						foreach($manifest->admin->js as $f)
							File::delete(PATH.'js/admin/'.$ns.'/'.$f, false);
						
						$plg->delete();
						
						Activity::log('deleted the plugin "'.$plg->_name.'"');
					
					}
					
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