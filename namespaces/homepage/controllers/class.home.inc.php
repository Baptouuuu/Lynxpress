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
	
	namespace Site\Homepage\Controllers;
	use \Site\Master\Controllers\Controller;
	use Exception;
	use \Library\Model\Setting;
	use \Site\Master\Helpers\Document;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Load homepage depending on homepage setting
		*
		* @package		Site
		* @subpackage	Homepage\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Home extends Controller{
	
		private $_controller = null;
		private $_setting = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->get_setting();
			$this->init_controller();
			
			$this->_title = $this->_controller->_title;
		
		}
		
		/**
			* Retrieve homepage setting
			*
			* @access	private
		*/
		
		private function get_setting(){
		
			try{
			
				$this->_setting = new Setting('homepage', '_key');
				$this->_setting->_data = json_decode($this->_setting->_data);
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Depending on the setting initiate some HTTP GET parameters
			* and then instantiate appropriate controller
			*
			* @access	private
		*/
		
		private function init_controller(){
		
			try{
			
				switch($this->_setting->_data->type){
				
					case 'post':
						$_GET['ns'] = 'posts';
						$ctl = '\\Site\\Posts\\Controllers\\';
						if($this->_setting->_data->view != 'all')
							$ctl .= 'View';
						else
							$ctl .= 'Home';
						break;
					
					case 'video':
						$_GET['ns'] = 'videos';
						$ctl = '\\Site\\Videos\\Controllers\\';
						if($this->_setting->_data->view != 'all')
							$ctl .= 'View';
						else
							$ctl .= 'Home';
						break;
					
					case 'album':
						$_GET['ns'] = 'albums';
						$ctl = '\\Site\\Albums\\Controllers\\';
						if($this->_setting->_data->view != 'all')
							$ctl .= 'View';
						else
							$ctl .= 'Home';
						break;
				
				}
				
				if($this->_setting->_data->view != 'all'){
				
					$_GET['ctl'] = 'view';
					$_GET['id'] = $this->_setting->_data->view;
				
				}
				
				$this->_controller = new $ctl();
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Display page menu
			*
			* @access	public
		*/
		
		public function display_menu(){
		
			$this->_controller->display_menu();
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->_controller->display_content();
		
		}
		
		/**
			* Display page sidebar
			*
			* @access	public
		*/
		
		public function display_sidebar(){
		
			$this->_controller->display_sidebar();
		
		}
	
	}

?>