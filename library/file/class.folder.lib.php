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
	
	namespace Library\File;
	use Exception;
	use RecursiveIteratorIterator;
	use RecursiveDirectoryIterator;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Folder class allows you to parse a directory and do actions on files discovered
		* Example:
		* <code>
		*	//Parse the directory from where the script is loaded and retrieve the list of files
		*	$folder = new Folder(__DIR__);
		*	$folder->_files;
		*
		*	//to delete them just call delete()
		*	$folder->delete();
		* </code>
		*
		* @package		Library
		* @subpackage	File
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
	*/
	
	class Folder{
	
		private $_directory = null;
		private $_files = null;
		
		/**
			* Class constructor
			*
			* @access	public
			* @param	string	[$dir]
		*/
		
		public function __construct($dir = ''){
		
			$this->_directory = $dir;
			
			if(!empty($this->_directory))
				$this->parse();
		
		}
		
		/**
			* Parse the wished directory and put the list of files in the _files attribute
			*
			* @access	public
			* @param	string	[$dir]
			* @return	object	Returns the current object
		*/
		
		public function parse($dir = ''){
		
			if(empty($dir) && empty($this->_directory))
				throw new Exception('No directory specified');
			elseif(!empty($dir))
				$this->_directory = $dir;
			
			$this->_files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_directory), RecursiveIteratorIterator::CHILD_FIRST);
			
			return $this;
		
		}
		
		/**
			* Delete all files and directory exisitng in _files attribute
			*
			* @access	public
			* @param	boolean	[$current] If set to true it will remove the current directory
		*/
		
		public function delete($current = false){
		
			foreach($this->_files as $f)
				if($f->isDir())
					@rmdir($f);
				else
					@unlink($f);
			
			if($current === true)
				@rmdir($this->_directory);
		
		}
	
	}

?>