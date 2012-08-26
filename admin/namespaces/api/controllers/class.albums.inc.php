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
	
	namespace Admin\Api\Controllers;
	use \Library\Variable\Get as VGet;
	use Exception;
	use \Library\Model\User;
	use \Library\File\File;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Controller to retrieve as JSON file a list of albums of the website
		* If no special parameter passed it will return the last 50 albums
		* If 'since' parameter passed it will return albums published since this date
		* If 'id' parameter passed it will return the album with the associated id
		* Example:
		* <code>
		*	//This return the last 50 albums
		*	$albums = new Curl('http://example.com/admin/?ns=api&ctl=albums');
		*
		*	//This will return albums published from October 19th, 1977
		*	$albums = new Curl('http://exmaple.com/admin/?ns=api&ctl=albums&since=1977-10-19');
		*
		*	//This will return the post with the id 1
		*	$first_album = new Curl('http://example.com/admin/?ns=api&ctl=albums&id=1');
		* </code>
		*
		* @package		Admin
		* @subpackage	Api\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Albums extends Master{
	
		private $_albums = null;
		private $_since = null;
		private $_id = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			if(VGet::since())
				$this->_since = VGet::since();
			elseif(VGet::id())
				$this->_id = VGet::id();
				
			$this->build_url();
			
			if(!$this->check_cache()){
				
				$this->get_albums();
				
				if(!empty($this->_id) && !empty($this->_albums))
					$this->get_images();
				
				$this->_albums = json_encode($this->_albums);
				
				$cache = new File();
				$cache->_content = $this->_albums;
				$cache->save($this->_cache);
			
			}else{
			
				$this->_albums = File::read($this->_cache)->_content;
			
			}
		
		}
		
		/**
			* Build url cache
			*
			* @access	private
		*/
		
		private function build_url(){
		
			if(!empty($this->_since))
				$this->_cache = parent::FOLDER.'albums-since-'.$this->_since.'.json';
			elseif(!empty($this->_id))
				$this->_cache = parent::FOLDER.'album-'.$this->_id.'.json';
			else
				$this->_cache = parent::FOLDER.'albums.json';
		
		}
		
		/**
			* Retrieve wished albums
			*
			* @access	private
		*/
		
		private function get_albums(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('_id', '_name', '_user', '_allow_comment', '_permalink', '_description', '_date');
				$to_read['condition_columns'][':s'] = '_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				$to_read['condition_types'][':t'] = 'AND';
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = '=';
				$to_read['condition_values'][':t'] = 'album';
				$to_read['value_types'][':t'] = 'str';
				
				if(!empty($this->_since)){
				
					$to_read['condition_types'][':d'] = 'AND';
					$to_read['condition_columns'][':d'] = '_date';
					$to_read['condition_select_types'][':d'] = '>';
					$to_read['condition_values'][':d'] = $this->_since;
					$to_read['value_types'][':d'] = 'str';
				
				}elseif(!empty($this->_id)){
				
					$to_read['condition_types'][':i'] = 'AND';
					$to_read['condition_columns'][':i'] = '_id';
					$to_read['condition_select_types'][':i'] = '=';
					$to_read['condition_values'][':i'] = $this->_id;
					$to_read['value_types'][':i'] = 'int';
				
				}else{
				
					$to_read['limit'] = array(0, 50);
				
				}
				
				$to_read['order'] = array('_date', 'DESC');
				
				$this->_albums = $this->_db->read($to_read);
				
				if(!empty($this->_albums))
					foreach($this->_albums as &$a){
					
						$user = new User($a['_user']);
						
						$a['_user'] = array('_id' => $user->_id, '_publicname' => $user->_publicname);
					
					}
			
			}catch(Exception $e){
			
				$this->_action_msg = $e->getMessage();
			
			}
		
		}
		
		/**
			* If a specific album is wished, we return also the associated pictures
			*
			* @access	private
		*/
		
		private function get_images(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('_id', '_name', '_permalink', '_description', '_date');
				$to_read['condition_columns'][':a'] = '_attachment';
				$to_read['condition_select_types'][':a'] = '=';
				$to_read['condition_values'][':a'] = $this->_albums[0]['_id'];
				$to_read['value_types'][':a'] = 'int';
				$to_read['condition_types'][':at'] = 'AND';
				$to_read['condition_columns'][':at'] = '_attach_type';
				$to_read['condition_select_types'][':at'] = '=';
				$to_read['condition_values'][':at'] = 'album';
				$to_read['value_types'][':at'] = 'str';
				
				$this->_albums[0]['pictures'] = $this->_db->read($to_read);
			
			}catch(Exception $e){
			
				$this->_action_msg = $e->getMessage();
			
			}
		
		}
		
		/**
			* Display the list of albums as JSON file
			*
			* @access	public
		*/
		
		public function display_content(){
		
			header('Content-type: application/json; charset=utf-8');
			
			echo $this->_albums;
		
		}
	
	}

?>