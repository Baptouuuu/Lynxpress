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
	
	namespace Library\Lang;
	use \Library\File\File;
	use \Library\Variable\Server as VServer;
	use Exception;
	
	defined('FOOTPRINT') or die();
	
	/**
		* I18n class
		*
		* Retrieve language json files and store them in a static array
		* When a call to i18n is made, it search in json file. If json not loaded it loads it.
		*
		* If the class can't translate a sentence it will return the called key
		*
		* @package		Library
		* @subpackage	Lang
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Lang{
	
		private static $_domains = array();
		private static $_lang = null;
		private $_key = null;
		private $_domain = null;
		private $_data = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	mixed [$key] Sentence to translate
			* @param	string [$domain] Namespace where is located the json file
			* @param	array [$data] Data to replace flags is sentence
		*/
		
		public function __construct($key, $domain, $data){
		
			$this->_key = $key;
			$this->_domain = $domain;
			$this->_data = $data;
		
			if(empty(self::$_lang))
				$this->get_user_lang();
			
			if(!isset(self::$_domains[$this->_domain]))
				$this->get_domain();
		
		}
		
		/**
			* Retrieve a json file from a domain, decode it and push it in domains attribute array
			*
			* @access	private
		*/
		
		private function get_domain(){
		
			try{
			
				$json = File::read('namespaces/'.$this->_domain.'/lang/'.self::$_lang.'.i18n.json')->_content;
				
				self::$_domains[$this->_domain] = json_decode($json, true);
				
				if(empty(self::$_domains[$this->_domain]))
					throw new Exception('Invalid Json');
			
			}catch(Exception $e){
			
				self::$_domains[$this->_domain] = array();
			
			}
		
		}
		
		/**
			* Detect user language
			*
			* @access	private
		*/
		
		private function get_user_lang(){
		
			$lang = substr(VServer::HTTP_ACCEPT_LANGUAGE('en'), 0, 2);
			
			if(!empty($lang))
				self::$_lang = $lang;
			else
				self::$_lang = 'en';
		
		}
		
		/**
			* Find translation for a specific key and replace data if _data attribute is not empty
			* If no translation found, it returns the key
			*
			* @access	public
			* @return	string
		*/
		
		public function translate(){
		
			$return = $this->_key;
			
			if(isset(self::$_domains[$this->_domain][$this->_key]) && !empty(self::$_domains[$this->_domain][$this->_key]))
				$return = self::$_domains[$this->_domain][$this->_key];
			
			if(!empty($this->_data) && is_array($this->_data))
				foreach($this->_data as $key => $value)
					$return = str_replace('%'.$key, $value, $return);
			
			return $return;
				
		
		}
		
		/**
			* Translate a sentence from a specific domain
			* data array has to be built as follows
			* <code>
			*	//key to translate
			*	$key = 'My sentence with a %param and %another';
			*
			*	//data array
			*	$data = array('param' => 'param value', 'another' => 'param value');
			* </code>
			*
			* @static
			* @access	public
			* @param	mixed [$key] Sentence to translate
			* @param	string [$domain] Namespace where is located the json file
			* @param	array [$data] Data to replace flags is sentence
			* @return	string
		*/
		
		public static function _($key, $domain = 'master', $data = array()){
		
			$i18n = new Lang($key, $domain, $data);
			
			return $i18n->translate();
		
		}
		
		/**
			* Return the lang attribute
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function get_lang(){
		
			return self::$_lang;
		
		}
	
	}

?>