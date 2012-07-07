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
	use \Admin\Plugins\Helpers\Plugins;
	use \Library\Lang\Lang;
	use \Admin\Plugins\Html\Bridge as Html;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Display the list of plugins to access to them
		*
		* @package		Admin
		* @subpackage	Plugins\Controller
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Bridge extends Master implements Controller{
	
		private $_plugins = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = Lang::_('Plugins');
			
			$this->get_plugins();
		
		}
		
		/**
			* Retrieve installed plugins manifests
			*
			* @access	private
		*/
		
		private function get_plugins(){
		
			$this->_plugins = Plugins::get_manifests();
		
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
			* Display plugins
			*
			* @access	private
		*/
		
		private function display_plugins(){
		
			Html::plugins('o');
			
			if(!empty($this->_plugins))
				foreach($this->_plugins as $p)
					Html::plugin(
						$p->infos->namespace,
						$p->infos->entry_point,
						$p->name
					);
			else
				Html::no_plugin();
			
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
			
			$this->display_plugins();
		
		}
	
	}

?>