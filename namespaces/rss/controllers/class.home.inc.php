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
	
	namespace Site\Rss\Controllers;
	use \Site\Master\Controllers\Controller;
	use Exception;
	use \Library\Database\Database;
	use \Library\Model\User;
	use SimpleXMLElement;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Generate a rss feed of published posts
		*
		* @package		Site
		* @subpackage	Rss\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Home extends Controller{
	
		private $_posts = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_display_html = false;
			
			$this->get_posts();
		
		}
		
		/**
			* Retrieve last 42 published posts
			*
			* @access	private
		*/
		
		private function get_posts(){
		
			try{
			
				$to_read['table'] = 'post';
				$to_read['columns'] = array('_id', '_title', '_content', '_date', '_user', '_permalink');
				$to_read['condition_columns'][':s'] = '_status';
				$to_read['condition_select_types'][':s'] = '=';
				$to_read['condition_values'][':s'] = 'publish';
				$to_read['value_types'][':s'] = 'str';
				$to_read['order'] = array('_date', 'DESC');
				$to_read['limit'] = array(0, 42);
				
				$this->_posts = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Post');
				
				if(!empty($this->_posts))
					foreach($this->_posts as &$p){
					
						$user = new User();
						$user->_id = $p->_user;
						$user->read('_publicname');
						
						$p->_user = $user;
					
					}
			
			}catch(Exception $e){
			
				header('HTTP/1.0 404 Not Found');
				exit;
			
			}
		
		}
		
		/**
			* Build xml file
			*
			* @access	private
		*/
		
		private function build_xml(){
		
			$xml = new SimpleXMLElement('<rss/>');
			$xml->addAttribute('version', '2.0');
			
			$channel = $xml->addChild('channel');
			$channel->addChild('title', WS_NAME);
			$channel->addChild('link', WS_URL);
			$channel->addChild('description', '');
			$channel->addChild('generator', 'Lynxpress');
			
			foreach($this->_posts as &$p){
			
				$item = $channel->addChild('item');
				
				$item->addChild('guid', $p->_id);
				$item->addChild('title', $p->_title);
				$item->addChild('description', htmlspecialchars($p->_content));
				$item->addChild('pubDate', date(DATE_RFC822, strtotime($p->_date)));
				$item->addChild('author', $p->_user->_publicname);
				$item->addChild('link', htmlspecialchars(Url::_(array('ns' => 'posts', 'ctl' => 'view', 'id' => $p->_permalink))));
			
			}
			
			echo $xml->asXML();
		
		}
		
		/**
			* Implementation of display_menu but not used here
			*
			* @access	public
		*/
		
		public function display_menu(){}
		
		/**
			* Display page content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			header('Content-Type: application/xml; charset=utf-8');
			$this->build_xml();
		
		}
		
		/**
			* Implementation of display_sidebar but not used here
			*
			* @access	public
		*/
		
		public function display_sidebar(){}
	
	}

?>