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
	
	namespace Admin\Plugins\Helpers;
	use \Library\Variable\Files;
	use \Library\File\File;
	use Exception;
	use \Library\Lang\Lang;
	use \Library\Model\Setting;
	use \Library\Curl\Curl;
	use \Admin\Activity\Helpers\Activity;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Easily install a plugin from a controller just by giving the source and the reference
		* For example:
		* <code>
		* 	//from a post source
		*	new Install('post', 'plugin');
		*
		* 	//or via the library
		* 	new Install('library', 1);
		*
		* 	//you can access to a potential error message like this
		*	$install = new Install('post', 'template');
		*	$install->_error;
		* </code>
		*
		* @package		Admin
		* @subpackage	Plugins\Helpers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	class Install{
	
		private $_source = null;
		private $_reference = null;
		private $_zip = null;
		private $_error = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	string			[$source] Can be 'post' or 'library'
			* @param	string|integer	[$reference] If the source is 'post' $reference will be the name of the input, otherwise it's the plugin id in library
		*/
		
		public function __construct($source, $reference){
		
			$this->_source = $source;
			$this->_reference = $reference;
			
			try{
			
				$this->get_zip();
				
				$this->install();
			
			}catch(Exception $e){
			
				$this->_error = $e->getMessage();
			
			}
		
		}
		
		/**
			* Retrieve plugin archive from the post or from the library
			*
			* @access	private
		*/
		
		private function get_zip(){
		
			if($this->_source == 'post'){
			
				$name = $this->_reference;
				
				if(Files::$name(false) === false)
					throw new Exception(Lang::_('Plugin archive not found', 'plugins'));
				
				$file = Files::$name();
				
				if($file['error'] != 0)
					throw new Exception(Lang::_('Plugin archive not found', 'plugins'));
				
				$this->_zip = File::read($file['tmp_name'])->_content;
			
			}elseif($this->_source == 'library'){
			
				$url = 'http://api.lynxpress.org/?ns=download&ctl=plugin&id='.$this->_reference;
				
				$curl = new Curl($url);
				
				$this->_zip = $curl->_content;
			
			}
			
			$zip = new File();
			$zip->_content = $this->_zip;
			$zip->save('tmp/plugin.zip');
		
		}
		
		/**
			* Make the install of the plugin
			* Decompress the archive to a temporary folder, move items into place
			* And finally create a reference in database
			*
			* @access	private
		*/
		
		private function install(){
		
			$tmp = 'tmp/plugin_'.md5(time()).'/';
			
			File::unzip('tmp/plugin.zip', $tmp);
			File::delete('tmp/plugin.zip');
			
			//if zip retrieved from github we search for the subdirectory made by github
			if($this->_source == 'library'){
			
				$subdir = @scandir($tmp);
				
				foreach($subdir as $d)
					if(!in_array($d, array('.', '..'))){
					
						$tmp .= $d.'/';
						break;
					
					}
			
			}
			//end search
			
			$manifest = json_decode(File::read($tmp.'manifest.json')->_content);
			
			if(!isset($manifest->name) && !isset($manifest->author) && !isset($manifest->url) && !isset($manifest->infos) && !isset($manifest->infos->namespace) && !isset($manifest->infos->compatibility) && !isset($manifest->infos->entry_point) && !isset($manifest->admin) && !isset($manifest->admin->core) && !isset($manifest->admin->css) && !isset($manifest->admin->js) && !isset($manifest->site) && !isset($manifest->site->core) && !isset($manifest->site->css) && !isset($manifest->site->js) && !isset($manifest->images) && !isset($manifest->install) && !isset($manifest->uninstall))
				throw new Exception(Lang::_('Plugin manifest invalid', 'plugins'));
			
			$ns = $manifest->infos->namespace;
			
			if(is_dir('namespaces/'.$ns) || is_dir(PATH.'namespaces/'.$ns) || is_dir(PATH.'css/admin/'.$ns) || is_dir(PATH.'css/site/'.$ns) || is_dir(PATH.'js/admin/'.$ns) || is_dir(PATH.'js/site/'.$ns) || is_dir(PATH.'images/'.$ns))
				throw new Exception(Lang::_('Another plugin use the same namespace', 'plugins'));
			
			//check that all files are readable
			foreach($manifest->admin->core as $f)
				File::read($tmp.'admin/core/'.$f);
			
			foreach($manifest->admin->css as $f)
				File::read($tmp.'admin/css/'.$f);
			
			foreach($manifest->admin->js as $f)
				File::read($tmp.'admin/js/'.$f);
			
			foreach($manifest->site->core as $f)
				File::read($tmp.'site/core/'.$f);
			
			foreach($manifest->site->css as $f)
				File::read($tmp.'site/css/'.$f);
			
			foreach($manifest->site->js as $f)
				File::read($tmp.'site/js/'.$f);
			
			foreach($manifest->images as $f)
				File::read($tmp.'images/'.$f);
			
			if($manifest->install === true)
				File::read($tmp.'class.install.inc.php');
			
			if($manifest->uninstall === true)
				File::read($tmp.'class.uninstall.inc.php');
			//end check
			
			//moving items into places
			foreach($manifest->admin->core as $f){
			
				File::move($tmp.'admin/core/'.$f, 'namespaces/'.$ns.'/'.$f);
				File::delete($tmp.'admin/core/'.$f);
			
			}
			
			foreach($manifest->admin->css as $f){
			
				File::move($tmp.'admin/css/'.$f, PATH.'css/admin/'.$ns.'/'.$f);
				File::delete($tmp.'admin/css/'.$f);
			
			}
			
			foreach($manifest->admin->js as $f){
			
				File::move($tmp.'admin/js/'.$f, PATH.'js/admin/'.$ns.'/'.$f);
				File::delete($tmp.'admin/js/'.$f);
			
			}
			
			foreach($manifest->site->core as $f){
			
				File::move($tmp.'site/core/'.$f, PATH.'namespaces/'.$ns.'/'.$f);
				File::delete($tmp.'site/core/'.$f);
			
			}
			
			foreach($manifest->site->css as $f){
			
				File::move($tmp.'site/css/'.$f, PATH.'css/site/'.$ns.'/'.$f);
				File::delete($tmp.'site/css/'.$f);
			
			}
			
			foreach($manifest->site->js as $f){
			
				File::move($tmp.'site/js/'.$f, PATH.'js/site/'.$ns.'/'.$f);
				File::delete($tmp.'site/js/'.$f);
			
			}
			
			foreach($manifest->images as $f){
			
				File::move($tmp.'images/'.$f, PATH.'images/'.$ns.'/'.$f);
				File::delete($tmp.'images/'.$f);
			
			}
			
			if($manifest->uninstall === true){
			
				File::move($tmp.'class.uninstall.inc.php', 'namespaces/'.$ns.'/class.uninstall.inc.php');
				File::delete($tmp.'class.uninstall.inc.php');
			
			}
			
			if($manifest->install === true){
			
				File::move($tmp.'class.install.inc.php', 'namespaces/'.$ns.'/class.install.inc.php');
				File::delete($tmp.'class.install.inc.php');
				
				//install made here in order to not duplicate this condition
				$class = '\\Admin\\'.$ns.'\\Install';
				new $class();
				
				File::delete('namespaces/'.$ns.'/class.install.inc.php');
			
			}
			//end moving items
			
			File::delete($tmp.'manifest.json');
			
			$plg = new Setting();
			$plg->_name = $manifest->name;
			$plg->_type = 'plugin';
			$plg->_data = json_encode($manifest);
			$plg->_key = 'plugin_'.$ns;
			
			$plg->create();
			
			Activity::log('added the plugin "'.$plg->_name.'"');
		
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