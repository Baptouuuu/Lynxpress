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
	
	namespace Site\Links\Controllers;
	use \Site\Master\Controllers\Controller;
	use \Site\Templates\Helpers\Template;
	use Exception;
	use \Library\Database\Database;
	use \Site\Links\Html\Home as Html;
	use \Site\Master\Helpers\Document;
	
	defined('FOOTPRINT') or die();
	
	/**
		* List all links to external websites
		*
		* @package		Site
		* @subpackage	Links\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class Home extends Controller{
	
		private $_links = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_title = 'Links';
			
			$this->get_links();
			
			Template::publish('links');
		
		}
		
		/**
			* Retrieve links
			*
			* @access	private
		*/
		
		private function get_links(){
		
			try{
			
				$to_read['table'] = 'link';
				$to_read['columns'] = array('_name', '_link', '_rss', '_notes');
				$to_read['order'] = array('_priority', 'DESC');
				
				$this->_links = $this->_db->read($to_read, Database::FETCH_CLASS, '\\Library\\Model\\Link');
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Display the list of links
			*
			* @access	private
		*/
		
		private function display_links(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('links'))
				$tpl::links('o');
			else
				Html::links('o');
			
			if(!empty($this->_links))
				if(Template::_callable('link'))
					foreach($this->_links as $l)
						$tpl::link(
							$l->_name,
							$l->_link,
							$l->_rss,
							$l->_notes
						);
				else
					foreach($this->_links as $l)
						Html::link(
							$l->_name,
							$l->_link,
							$l->_rss,
							$l->_notes
						);
			else
				if(Template::_callable('no_data'))
					$tpl::no_data();
				else
					Html::no_data();
			
			if(Template::_callable('links'))
				$tpl::links('c');
			else
				Html::links('c');
		
		}
		
		/**
			* Display page menu
			*
			* @access	public
		*/
		
		public function display_menu(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('menu'))
				$tpl::menu($this->_menu);
			else
				Html::menu($this->_menu);
		
		}
		
		/**
			* Display page main content
			*
			* @access	public
		*/
		
		public function display_content(){
		
			$this->display_links();
		
		}
		
		/**
			* Display page sidebar
			*
			* @access	public
		*/
		
		public function display_sidebar(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('search'))
				$tpl::search();
			else
				Html::search();
			
			if(Template::_callable('infos'))
				$tpl::infos();
			else
				Html::infos();
		
		}
	
	}

?>