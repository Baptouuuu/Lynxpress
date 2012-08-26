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
	
	namespace Site\Videos\Controllers;
	use \Site\Master\Controllers\Controller;
	use Exception;
	use \Site\Templates\Helpers\Template;
	use \Admin\Categories\Helpers\Categories;
	use \Site\Master\Helpers\Document;
	use \Library\Database\Database;
	use \Library\Model\User;
	use \Library\Model\Media;
	use \Library\Variable\Get as VGet;
	use \Site\Videos\Html\View as Html;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Display a specific video
		*
		* @package		Site
		* @subpackage	Videos\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @final
	*/
	
	final class View extends Controller{
	
		private $_video = null;
		private $_id = null;
		private $_categories = null;
		
		/**
			* Class constructor
			*
			* @access	public
		*/
		
		public function __construct(){
		
			parent::__construct();
			
			$this->_id = VGet::id(false);
			
			$this->get_video();
			$this->get_categories();
			
			$this->_title = $this->_video->_name;
			
			Template::publish('videos.view.'.$this->_id);
		
		}
		
		/**
			* Retrieve wished video
			*
			* @access	private
		*/
		
		private function get_video(){
		
			try{
			
				if($this->_id === false)
					throw new Exception('Video id missing');
				
				$this->_video = new Media($this->_id);
				
				$user = new User();
				$user->_id = $this->_video->_user;
				$user->read('_publicname');
				$user->read('_email');
				$user->read('_website');
				$user->read('_msn');
				$user->read('_twitter');
				$user->read('_facebook');
				$user->read('_google');
				$user->read('_bio');
				
				$this->_video->_user = $user;
				
				$attachment = $this->_video->_attachment;
				
				if(!empty($attachment) && $this->_video->_attach_type == 'fallback')
					$this->_video->fallback = new Media($this->_video->_attachment);
				else
					$this->_video->fallback = false;
				
				$this->_video->_extra = json_decode($this->_video->_extra);
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Retrieve posts categories
			*
			* @access	private
		*/
		
		private function get_categories(){
		
			try{
			
				$this->_categories = Categories::get_type('video');
			
			}catch(Exception $e){
			
				Document::e404($e->getMessage(), __FILE__, __LINE__);
			
			}
		
		}
		
		/**
			* Display wished video
			*
			* @access	private
		*/
		
		private function display_video(){
		
			$tpl = $this->_template;
			
			if(Template::_callable('videos'))
				$tpl::videos('o', $this->_video->_name);
			else
				Html::videos('o', $this->_video->_name);
			
			if(Template::_callable('video'))
				$tpl::video(
					$this->_video->_id,
					$this->_video->_name,
					$this->_video->_type,
					$this->_video->_user,
					$this->_video->_permalink,
					$this->_video->_description,
					$this->_video->_date,
					$this->_video->_extra,
					$this->_video->fallback
				);
			else
				Html::video(
					$this->_video->_id,
					$this->_video->_name,
					$this->_video->_type,
					$this->_video->_user,
					$this->_video->_permalink,
					$this->_video->_description,
					$this->_video->_date,
					$this->_video->_extra,
					$this->_video->fallback
				);
			
			if(Template::_callable('videos'))
				$tpl::videos('c');
			else
				Html::videos('c');
		
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
		
			$this->display_video();
		
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
				
			if(Template::_callable('author'))
				$tpl::author(
					$this->_video->_user->_publicname,
					$this->_video->_user->_email,
					$this->_video->_user->_website,
					$this->_video->_user->_msn,
					$this->_video->_user->_twitter,
					$this->_video->_user->_facebook,
					$this->_video->_user->_google,
					$this->_video->_user->_bio
				);
			else
				Html::author(
					$this->_video->_user->_publicname,
					$this->_video->_user->_email,
					$this->_video->_user->_website,
					$this->_video->_user->_msn,
					$this->_video->_user->_twitter,
					$this->_video->_user->_facebook,
					$this->_video->_user->_google,
					$this->_video->_user->_bio
				);
			
			if(Template::_callable('categories'))
				$tpl::categories($this->_categories);
			else
				Html::categories($this->_categories);
		
		}
	
	}

?>