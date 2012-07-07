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
	use \Admin\Master\Interfaces\Controller;
	use \Library\Variable\Get as VGet;
	use Exception;
	use \Library\Model\User;
	use \Library\Model\Media;
	use \Library\File\File;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Controller to retrieve as JSON file a list of videos of the website
		* If no special parameter passed it will return the last 50 videos
		* If 'since' parameter passed it will return videos published since this date
		* If 'id' parameter passed it will return the video with the associated id
		* Example:
		* <code>
		*	//This return the last 50 videos
		*	$videos = new Curl('http://example.com/admin/?ns=api&ctl=videos');
		*
		*	//This will return videos published from October 19th, 1977
		*	$videos = new Curl('http://exmaple.com/admin/?ns=api&ctl=videos&since=1977-10-19');
		*
		*	//This will return the video with the id 1
		*	$first_video = new Curl('http://example.com/admin/?ns=api&ctl=videos&id=1');
		* </code>
		*
		* @package		Admin
		* @subpackage	Api\Controllers
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @final
	*/
	
	final class Videos extends Master implements Controller{
	
		private $_videos = null;
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
			
				$this->get_videos();
				
				$this->_videos = json_encode($this->_videos);
				
				$cache = new File();
				$cache->_content = $this->_videos;
				$cache->save($this->_cache);
			
			}else{
			
				$this->_videos = File::read($this->_cache)->_content;
			
			}
		
		}
		
		/**
			* Build url cache
			*
			* @access	private
		*/
		
		private function build_url(){
		
			if(!empty($this->_since))
				$this->_cache = parent::FOLDER.'videos-since-'.$this->_since.'.json';
			elseif(!empty($this->_id))
				$this->_cache = parent::FOLDER.'video-'.$this->_id.'.json';
			else
				$this->_cache = parent::FOLDER.'videos.json';
		
		}
		
		/**
			* Retrieve wished videos
			*
			* @access	private
		*/
		
		private function get_videos(){
		
			try{
			
				$to_read['table'] = 'media';
				$to_read['columns'] = array('_id', '_name', '_user', '_permalink', '_description', '_date', '_attachment', '_attach_type');
				$to_read['condition_columns'][':s'] = '_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				$to_read['condition_types'][':t'] = 'AND';
				$to_read['condition_columns'][':t'] = '_type';
				$to_read['condition_select_types'][':t'] = 'LIKE';
				$to_read['condition_values'][':t'] = 'video%';
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
				
				$this->_videos = $this->_db->read($to_read);
				
				if(!empty($this->_videos))
					foreach($this->_videos as &$v){
					
						$user = new User($v['_user']);
						$v['_user'] = array('_id' => $user->_id, '_publicname' => $user->_publicname);
						
						if(!empty($v['_attachment']) && $v['_attach_type'] == 'fallback'){
						
							$f = new Media($v['_attachment']);
							$v['fallback'] = array('_id' => $f->_id, '_name' => $f->_name, '_embed_code' => $f->_embed_code);
						
						}
					
					}
			
			}catch(Exception $e){
			
				$this->_action_msg = $e->getMessage();
			
			}
		
		}
		
		/**
			* Display the list of videos as JSON file
			*
			* @access	public
		*/
		
		public function display_content(){
		
			header('Content-type: application/json; charset=utf-8');
			
			echo $this->_videos;
		
		}
	
	}

?>