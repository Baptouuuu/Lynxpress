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
	
	namespace Admin\Templates\Controllers;
	use \Admin\Master\Controllers\Controller as Master;
	use \Admin\Master\Interfaces\Controller;
	use \Library\Lang\Lang;
	use \Admin\ActionMessages\ActionMessages;
	use \Admin\Templates\Html\Manage as Html;
	use \Library\Database\Database;
	use Exception;
	use \Library\Model\Setting;
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Variable\Post as VPost;
	use \Library\File\File;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allow user to browse available templates and install them
		*
		* @package		Admin
		* @subpackage	Templates\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Manage extends Master implements Controller{
	
		private $_templates = null;
		private $_current = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Templates');
			
			if($this->_user->_permissions->setting){
			
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.labels.js');
				Helper::add_header_link('js', WS_URL.'js/admin/core/viewModel.button_confirm.js');
				
				$this->get_current();
				
				$this->update();
				$this->delete();
				
				$this->get_templates();
			
			}
		
		}
		
		/**
			* Retrieve templates from the lynxpress website
			*
			* @access	private
		*/
		
		private function get_templates(){
		
			try{
			
				$to_read['table'] = 'setting';
				$to_read['columns'] = array('*');
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'template';
				$to_read['value_types'][':t'] = 'str';
				
				$this->_templates = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Setting');
				
				if(!empty($this->_templates))
					foreach($this->_templates as &$t)
						$t->_data = json_decode($t->_data);
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
			
			}
		
		}
		
		/**
			* Retrieve template curently used
			*
			* @access	private
		*/
		
		private function get_current(){
		
			try{
			
				$this->_current = new Setting('current_template', '_key');
			
			}catch(Exception $e){
			
				$this->_action_msg .= ActionMessages::custom_wrong($e->getMessage());
				
				$this->_current = new Setting();
			
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
			* Display installed templates
			*
			* @access	private
		*/
		
		private function display_templates(){
		
			Html::actions($this->_current->_name);
			
			Html::templates('o');
			
			if(!empty($this->_templates))
				foreach($this->_templates as $t)
					Html::template(
						$t->_id,
						$t->_name,
						$t->_data->author,
						$t->_data->url
					);
			
			Html::templates('c');
		
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
				
				Html::form('o', 'post', Url::_(array('ns' => 'templates')));
				
				$this->display_templates();
				
				Html::form('c');
			
			}else{
			
				echo ActionMessages::part_no_perm();
			
			}
		
		}
		
		/**
			* Changed template used
			*
			* @access	private
		*/
		
		private function update(){
		
			if(VPost::update() && VPost::template()){
			
				try{
				
					$tpl = new Setting(VPost::template());
					$tpl->_data = json_decode($tpl->_data);
					
					$this->_current->_name = $tpl->_name;
					$this->_current->_data = $tpl->_data->infos->namespace;
					
					$this->_current->update('_name');
					$this->_current->update('_data');
					
					Activity::log('changed the template to "'.$this->_current->_name.'"');
					
					$result = true;
				
				}catch(Exception $e){
				
					$result = $e->getMessage();
				
				}
				
				$this->_action_msg .= ActionMessages::updated($result);
			
			}
		
		}
		
		/**
			* Delete a template and its files
			*
			* @access	private
		*/
		
		private function delete(){
		
			if(VPost::delete() && VPost::template() && $this->_user->_permissions->delete){
			
				try{
				
					$tpl = new Setting(VPost::template());
					
					$manifest = json_decode($tpl->_data, true);
					
					if($manifest['infos']['namespace'] == 'main')
						throw new Exception(Lang::_('Default template can\'t be deleted', 'templates'));
					
					if($manifest['infos']['namespace'] == $this->_current->_data)
						throw new Exception(Lang::_('The template "%tpl" is in use', 'templates', array('tpl' => $tpl->_name)));
					
					$nm = $manifest['infos']['namespace'];
					
					if(isset($manifest['files']['js']))
						foreach($manifest['files']['js'] as $f)
							File::delete(PATH.'js/templates/'.$nm.'/'.$f, false);
					
					if(isset($manifest['files']['css']))
						foreach($manifest['files']['css'] as $f)
							File::delete(PATH.'css/templates/'.$nm.'/'.$f, false);
					
					foreach($manifest['files']['core'] as $f)
						File::delete(PATH.'templates/'.$nm.'/'.$f, false);
					
					$tpl->delete();
					
					Activity::log('deleted the template "'.$tpl->_name.'"');
					
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