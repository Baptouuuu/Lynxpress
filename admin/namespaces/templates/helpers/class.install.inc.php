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
	
	namespace Admin\Templates\Helpers;
	use \Library\Variable\Files;
	use \Library\File\File;
	use Exception;
	use \Library\Lang\Lang;
	use \Library\Model\Setting;
	use \Library\Curl\Curl;
	use \Admin\Activity\Helpers\Activity;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Easily install a template from a controller just by giving the source and the reference
		* For example:
		* <code>
		* 	//from a post source
		*	new Install('post', 'template');
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
		* @subpackage	Templates\Helpers
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
			* @param	string|integer	[$reference] If the source is 'post' $reference will be the name of the input, otherwise it's the template id in library
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
			* Retrieve template archive from the post or from the library
			*
			* @access	private
		*/
		
		private function get_zip(){
		
			if($this->_source == 'post'){
			
				$name = $this->_reference;
			
				if(Files::$name(false) === false)
					throw new Exception(Lang::_('Template archive not found', 'templates'));
				
				$file = Files::$name();
				
				if($file['error'] != 0)
					throw new Exception(Lang::_('Template archive not found', 'templates'));
				
				$this->_zip = File::read($file['tmp_name'])->_content;
			
			}elseif($this->_source == 'library'){
			
				$url = 'http://api.lynxpress.org/?ns=download&ctl=template&id='.$this->_reference;
				
				$curl = new Curl($url);
				
				$this->_zip = $curl->_content;
			
			}
			
			$zip = new File();
			$zip->_content = $this->_zip;
			$zip->save('tmp/template.zip');
		
		}
		
		/**
			* Make the install of the template
			* Decompress the archive to a temporary folder, move items into place
			* And finally create a reference in database
			*
			* @access	private
		*/
		
		private function install(){
		
			$tmp = 'tmp/template_'.md5(time()).'/';
			
			File::unzip('tmp/template.zip', $tmp);
			File::delete('tmp/template.zip');
			
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
			
			$manifest = json_decode(File::read($tmp.'manifest.json')->_content, true); 
			
			if(!isset($manifest['name']) && !isset($manifest['author']) && !isset($manifest['url']) && !isset($manifest['infos']) && !isset($manifest['infos']['namespace']) && !isset($manifest['infos']['compatibility']) && !isset($manifest['files']) && !isset($manifest['files']['core']))
				throw new Exception(Lang::_('Template manifest invalid', 'templates'));
			
			if(is_dir(PATH.'templates/'.$manifest['infos']['namespace']))
				throw new Exception(Lang::_('Template already exist', 'templates'));
			
			if(!in_array(WS_VERSION, $manifest['infos']['compatibility']))
				throw new Exception(Lang::_('Template not compatible with your Lynxpress version', 'templates'));
			
			//check if all files of the manifest exist in the archive
			if(isset($manifest['files']['css']))
				foreach($manifest['files']['css'] as $f)
					File::read($tmp.'files/css/'.$f);
			
			if(isset($manifest['files']['js']))
				foreach($manifest['files']['js'] as $f)
					File::read($tmp.'files/js/'.$f);
			
			foreach($manifest['files']['core'] as $f)
				File::read($tmp.'files/core/'.$f);
			//end check
			
			//move files into place
			if(isset($manifest['files']['css']))
				foreach($manifest['files']['css'] as $f){
				
					File::move($tmp.'files/css/'.$f, PATH.'css/templates/'.$manifest['infos']['namespace'].'/'.$f);
					File::delete($tmp.'files/css/'.$f);
				
				}
			
			if(isset($manifest['files']['js']))
				foreach($manifest['files']['js'] as $f){
				
					File::move($tmp.'files/js/'.$f, PATH.'js/templates/'.$manifest['infos']['namespace'].'/'.$f);
					File::delete($tmp.'files/js/'.$f);
				
				}
			
			foreach($manifest['files']['core'] as $f){
			
				File::move($tmp.'files/core/'.$f, PATH.'templates/'.$manifest['infos']['namespace'].'/'.$f);
				File::delete($tmp.'files/core/'.$f);
			
			}
			
			File::delete($tmp.'manifest.json');
			
			$setting = new Setting();
			$setting->_name = $manifest['name'];
			$setting->_type = 'template';
			$setting->_data = json_encode($manifest);
			$setting->_key = 'template_'.$manifest['infos']['namespace'];
			$setting->create();
			
			Activity::log('added the template "'.$setting->_name.'"');
		
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