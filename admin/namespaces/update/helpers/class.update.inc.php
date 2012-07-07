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
	
	namespace Admin\Update\Helpers;
	use \Library\Curl\Curl;
	use \Library\File\File;
	use Exception;
	use \Admin\Activity\Helpers\Activity;
	use \Library\Lang\Lang;
	use \Library\Database\Backup;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Easily check if there's a system update available, and install it
		* Example
		* <code>
		*	//check if there's an update
		*	$boolean = Update:check();
		*
		*	//install the update
		*	$update = new Update();
		*
		*	//access a possible error
		*	$update->_error;
		* </code>
		*
		* @package		Admin
		* @subpackage	Update\Helpers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	class Update{
	
		const URL = 'http://api.lynxpress.org/';
		private $_error = null;
		
		/**
			* Class constructor, initialize the system update
			*
			* @access	public
		*/
		
		public function __construct(){
		
			try{
			
				$this->make_backup();
				
				$this->get_zip();
				
				$this->install();
			
			}catch(Exception $e){
			
				$this->_error = $e->getMessage();
			
			}
		
		}
		
		/**
			* Make a backup of the database before any action
			*
			* @access	private
		*/
		
		private function make_backup(){
		
			$bk = new Backup();
			$bk->save('backup/dump-'.date('Y-m-d-H:i:s').'.sql');
			
			$html = new File();
			$html->_content = '<!--The Lynx is not here!-->';
			$html->save('backup/index.html');
			
			$mail = new Mail(WS_EMAIL, 'Databse dump made before update at '.date('Y-m-d H:i:s'), $bk->_sql);
			$mail->send();
		
		}
		
		/**
			* Retrieve zip from lynxpress.org
			*
			* @access	private
		*/
		
		private function get_zip(){
		
			$curl = new Curl(self::URL.'?ns=download&update=true');
			
			$zip = new File();
			$zip->_content = $curl->_content;
			$zip->save('tmp/update.zip');
		
		}
		
		/**
			* Unzip the archive and move items into places
			*
			* @access	private
		*/
		
		private function install(){
		
			$tmp = 'tmp/update_'.md5(time()).'/';
			
			File::unzip('tmp/update.zip', $tmp);
			File::delete('tmp/update.zip');
			
			$subdir = @scandir($tmp);
			
			foreach($subdir as $d)
				if(!in_array($d, array('.', '..'))){
				
					$tmp .= $d.'/';
					break;
				
				}
			
			$curl = new Curl(self::URL.'?ns=download&ctl=manifest');
			$manifest = json_decode($curl->_content);
			
			if(!isset($manifest->version) && !isset($manifest->admin) && !isset($manifest->site) && !isset($manifest->images) && !isset($manifest->template))
				throw new Exception(Lang::_('Invalid manifest', 'update'));
			
			//check all files are readable
			foreach($manifest->admin->core as $f)
				File::read($tmp.'admin/namespaces/'.$f);
			
			foreach($manifest->admin->css as $f)
				File::read($tmp.'css/admin/'.$f);
			
			foreach($manifest->admin->js as $f)
				File::read($tmp.'js/admin/core/'.$f);
			
			foreach($manifest->site->core as $f)
				File::read($tmp.'namespaces/'.$f);
			
			foreach($manifest->site->css as $f)
				File::read($tmp.'css/site/'.$f);
			
			foreach($manifest->site->js as $f)
				File::read($tmp.'js/site/core/'.$f);
			
			foreach($manifest->images as $f)
				File::read($tmp.'images/'.$f);
			
			foreach($manifest->template->core as $f)
				File::read($tmp.'templates/main/'.$f);
			
			foreach($manifest->template->css as $f)
				File::read($tmp.'css/templates/main/'.$f);
			
			foreach($manifest->template->js as $f)
				File::read($tmp.'js/templates/main/'.$f);
			
			foreach($manifest->others->src as $f)
				File::read($tmp.$f);
			
			if($manifest->update === true)
				File::read($tmp.'admin/namespaces/update/helpers/class.runupdate.inc.php');
			//end check
			
			//moving items into places
			foreach($manifest->admin->core as $f){
			
				File::move($tmp.'admin/namespaces/'.$f, 'namespaces/'.$f);
				File::delete($tmp.'admin/namespaces/'.$f);
			
			}
			
			foreach($manifest->admin->css as $f){
			
				File::move($tmp.'css/admin/'.$f, PATH.'css/admin/'.$f);
				File::delete($tmp.'css/admin/'.$f);
			
			}
			
			foreach($manifest->admin->js as $f){
			
				File::move($tmp.'js/admin/core/'.$f, PATH.'js/admin/core/'.$f);
				File::delete($tmp.'js/admin/core/'.$f);
			
			}
			
			foreach($manifest->site->core as $f){
			
				File::move($tmp.'namespaces/'.$f, PATH.'namespaces/'.$f);
				File::delete($tmp.'namespaces/'.$f);
			
			}
			
			foreach($manifest->site->css as $f){
			
				File::move($tmp.'css/site/'.$f, PATH.'css/site/'.$f);
				File::delete($tmp.'css/site/'.$f);
			
			}
			
			foreach($manifest->site->js as $f){
			
				File::move($tmp.'js/site/core/'.$f, PATH.'js/site/core/'.$f);
				File::delete($tmp.'js/site/core/'.$f);
			
			}
			
			foreach($manifest->images as $f){
			
				File::move($tmp.'images/'.$f, PATH.'images/'.$f);
				File::delete($tmp.'images/'.$f);
			
			}
			
			foreach($manifest->template->core as $f){
			
				File::move($tmp.'templates/main/'.$f, PATH.'templates/main/'.$f);
				File::delete($tmp.'templates/main/'.$f);
			
			}
			
			foreach($manifest->template->css as $f){
			
				File::move($tmp.'css/templates/main/'.$f, PATH.'css/templates/main/'.$f);
				File::delete($tmp.'css/templates/main/'.$f);
			
			}
			
			foreach($manifest->template->js as $f){
			
				File::move($tmp.'js/templates/main/'.$f, PATH.'js/templates/main/'.$f);
				File::delete($tmp.'js/templates/main/'.$f);
			
			}
			
			foreach($manifest->others->src as $key => $f){
			
				File::move($tmp.$f, $manifest->others->dest[$key]);
				File::delete($tmp.$f);
			
			}
			
			if($manifest->update === true){
			
				File::move($tmp.'admin/namespaces/update/helpers/class.runupdate.inc.php', 'namespaces/update/helpers/class.runupdate.inc.php');
				$update = new \Admin\Update\Helpers\RunUpdate();
				
				$error = $update->_error;
				
				if(!empty($error))
					throw new Exception($error);
				
				File::delete($tmp.'admin/namespaces/update/helpers/class.runupdate.inc.php');
				File::delete('namespaces/update/helpers/class.runupdate.inc.php');
			
			}
			
			$config = File::read(PATH.'config.php');
			$config->_content = str_replace('(\'WS_VERSION\', \''.WS_VERSION.'\')', '(\'WS_VERSION\', \''.$manifest->version.'\')', $config->_content);
			$config->save();
			
			Activity::log('updated the website to the version "'.$manifest->version.'"');
		
		}
		
		/**
			* Check if there's an update available
			*
			* @static
			* @access	public
			* @return	boolean
		*/
		
		public static function check(){
		
			try{
			
				$curl = new Curl(self::URL.'?ns=download&ctl=manifest');
				
				$manifest = json_decode($curl->_content);
				
				if(!empty($manifest) && $manifest->version > WS_VERSION)
					return true;
				else
					return false;
			
			}catch(Exception $e){
			
				return false;
			
			}
		
		}
		
		/**
			* Allow to access to error attribute
			*
			* @access	public
			* @param	string	[$attr]
			* @return	string
		*/
		
		public function __get($attr){
		
			if(in_array($attr, array('_error')))
				return $this->$attr;
			else
				return 'The lynx is not here!';
		
		}
	
	}

?>