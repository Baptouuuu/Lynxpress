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
	
	namespace Site\Install\Controllers;
	use \Site\Install\Html\Home as Html;
	use \Library\Variable\Post as VPost;
	use Exception;
	use \Site\Install\Helpers\Database;
	use \Site\Install\Helpers\Config;
	use \Library\Variable\Get as VGet;
	use \Library\File\File;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Lynxpress installer controller
		*
		* @package		Site
		* @subpackage	Install\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Home{
	
		private $_display_html = null;
		private $_header = null;
		private $_footer = null;
		private $_error = null;
		private $_config = null;
		private $_success = null;
		private $_without_config = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			if(file_exists(PATH.'config.php') && Database::installed()){
			
				header('Location: index.php');
				exit();
			
			}
			
			$this->_display_html = true;
			
			$this->_header = 'namespaces/install/files/header.php';
			$this->_footer = 'namespaces/install/files/footer.php';
			
			$this->_success = false;
			$this->_without_config = false;
			
			if(VGet::without_config(false) || file_exists(PATH.'config.php'))
				$this->_without_config = true;
			
			$this->run_install();
		
		}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			if($this->_success == false){
			
				Html::form('o');
				
				Html::welcome();
				
				if($this->_error == 'missing infos')
					Html::missing_infos();
				elseif($this->_error == 'unknown host')
					Html::unknown_host();
				elseif($this->_error == 'error create database')
					Html::error_create_database();
				elseif($this->_error == 'error create table')
					Html::error_create_table();
				elseif($this->_error == 'config sample missing')
					Html::config_missing();
				elseif($this->_error == 'error create config')
					Html::error_config($this->_config);
				elseif($this->_error == 'error fill')
					Html::error_fill();
				
				if($this->_without_config === false){
				
					Html::database();
					Html::website();
				
				}
				
				Html::user();
				Html::install();
				
				Html::form('c');
			
			}else{
			
				Html::success();
			
			}
		
		}
		
		/**
			* Run the install
			*
			* @access	private
		*/
		
		private function run_install(){
		
			if(VPost::run(false)){
			
				try{
				
					if($this->_without_config === false){
					
						if(!VPost::db_host(false) || !VPost::db_name(false) || !VPost::db_user(false) || !VPost::db_pwd(false) || !VPost::ws_name(false) || !VPost::ws_url(false) || !VPost::ws_email(false) || !VPost::username(false) || !VPost::password(false))
							throw new Exception('missing infos');
						
						Database::create(
							VPost::db_host(),
							VPost::db_name(),
							VPost::db_user(),
							VPost::db_pwd(),
							VPost::db_prefix()
						);
						
						Config::create(
							VPost::db_host(),
							VPost::db_name(),
							VPost::db_user(),
							VPost::db_pwd(),
							VPost::db_prefix(),
							VPost::ws_name(),
							VPost::ws_url(),
							VPost::ws_email()
						);
						
						require_once PATH.'config.php';
						
						Database::fill(
							VPost::username(),
							VPost::password(),
							VPost::ws_email()
						);
					
					}else{
					
						require_once PATH.'config.php';
						
						Database::create(
							DB_HOST,
							DB_NAME,
							DB_USER,
							DB_PWD,
							DB_PREFIX
						);
						
						Database::fill(
							VPost::username(),
							VPost::password(),
							WS_EMAIL
						);
					
					}
					
					File::delete('admin/namespaces/update/helpers/class.runupdate.inc.php', false);
					
					$this->_success = true;
				
				}catch(Exception $e){
				
					$this->_error = $e->getMessage();
					
					if($this->_error == 'error create config')
						$this->_config = Config::build(
							VPost::db_host(),
							VPost::db_name(),
							VPost::db_user(),
							VPost::db_pwd(),
							VPost::db_prefix(),
							VPost::ws_name(),
							VPost::ws_url(),
							VPost::ws_email()
						);
				
				}
			
			}
		
		}
		
		/**
			* Uncacheable page, method unused but implemented to prevent error message
			* if the user try to access install page through index.php file
			*
			* @static
			* @access	public
			* @return	boolean
		*/
		
		public static function cacheable(){
		
			return false;
		
		}
		
		/**
			* Function to get attributes from outside the object
			*
			* @access	public
			* @param	string [$attr]
			* @return	mixed
		*/
		
		public function __get($attr){
		
			if(in_array($attr, array('_display_html', '_header', '_footer')))
				return $this->$attr;
			else
				return 'The Lynx is not here!';
		
		}
	
	}

?>