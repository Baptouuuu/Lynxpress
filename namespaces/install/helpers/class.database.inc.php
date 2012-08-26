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
	
	namespace Site\Install\Helpers;
	use Exception;
	use PDO;
	use \Library\Model\User;
	use \Library\Model\Category;
	use \Library\Model\Setting;
	use \Library\Model\Post;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Helper to create lynxpress database
		*
		* @package		Site
		* @subpackage	Install\Helpers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Database{
	
		private static $_db = null;
		
		/**
			* Init database creation
			*
			* @static
			* @access	public
			* @param	string	[$host]
			* @param	string	[$name]
			* @param	string	[$user]
			* @param	string	[$pwd]
			* @param	string	[$prefix]
		*/
		
		public static function create($host, $name, $user, $pwd, $prefix){
		
			try{
			
				self::test_connection($host, $name, $user, $pwd);
			
			}catch(Exception $e){
			
				if($e->getMessage() == 'unknown database')
					self::create_database($host, $name, $user, $pwd);
				else
					throw new Exception($e->getMessage());
			
			}
			
			//tables creation
			self::create_user($prefix);
			self::create_activity($prefix);
			self::create_category($prefix);
			self::create_comment($prefix);
			self::create_link($prefix);
			self::create_media($prefix);
			self::create_post($prefix);
			self::create_session($prefix);
			self::create_setting($prefix);
		
		}
		
		/**
			* Test the database connection
			*
			* @static
			* @access	private
			* @param	string	[$host]
			* @param	string	[$name]
			* @param	string	[$user]
			* @param	string	[$pwd]
		*/
		
		private static function test_connection($host, $name, $user, $pwd){
		
			try{
			
				self::$_db = new PDO('mysql:dbname='.$name.';host='.$host.';', $user, $pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
			
			}catch(Exception $e){
			
				if($e->getMessage() == 'SQLSTATE[HY000] [2005] Unknown MySQL server host \''.$host.'\'')
					throw new Exception('unknown host');
				
				if($e->getMessage() == 'SQLSTATE[42000] [1049] Unknown database \''.$name.'\'')
					throw new Exception('unknown database');
			
			}
		
		}
		
		/**
			* Create the database on the mysql server
			*
			* @static
			* @access	private
			* @param	string	[$host]
			* @param	string	[$name]
			* @param	string	[$user]
			* @param	string	[$pwd]
		*/
		
		private static function create_database($host, $name, $user, $pwd){
		
			try{
			
				self::$_db = new PDO('mysql:host='.$host.';', $user, $pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
				
				$result = self::$_db->query('CREATE DATABASE `'.$name.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
				
				if($result == false || $result->errorCode() != 00000)
					throw new Exception('error create database');
				
				self::$_db->query('USE `'.$name.'`');
			
			}catch(Exception $e){
			
				if($e->getMessage() == 'error create database')
					throw new Exception('error create database');
				else
					throw new Exception('unknown error');
			
			}
		
		}
		
		/**
			* Create user table
			*
			* @static
			* @access	private
			* @param	string	[$prefix]
		*/
		
		private static function create_user($prefix){
		
			try{
			
				$sql = 'CREATE TABLE IF NOT EXISTS `'.$prefix.'user` (
					`_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`_username` varchar(20) NOT NULL DEFAULT \'\',
					`_nickname` varchar(20) DEFAULT \'\',
					`_firstname` tinytext,
					`_lastname` tinytext,
					`_publicname` tinytext NOT NULL,
					`_password` tinytext NOT NULL,
					`_email` varchar(128) NOT NULL DEFAULT \'\',
					`_website` tinytext,
					`_msn` tinytext,
					`_twitter` tinytext,
					`_facebook` tinytext,
					`_google` tinytext,
					`_bio` text,
					`_role` varchar(20) NOT NULL DEFAULT \'\',
					`_active` int(1) NOT NULL DEFAULT \'1\' COMMENT \'set to 0 when a user is deleted\',
					PRIMARY KEY (`_id`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
				
				$result = self::$_db->query($sql);
				
				if($result == false || $result->errorCode() != 00000)
					throw new Exception('error');
			
			}catch(Exception $e){
			
				throw new Exception('error create table');
			
			}
		
		}
		
		/**
			* Create activity table
			*
			* @static
			* @access	private
			* @param	string	[$prefix]
		*/
		
		private static function create_activity($prefix){
		
			try{
			
				$sql = 'CREATE TABLE IF NOT EXISTS `'.$prefix.'activity` (
					`user_id` int(11) unsigned NOT NULL,
					`_data` tinytext NOT NULL,
					`_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
					KEY `idx_user_id` (`user_id`),
					CONSTRAINT `dev_activity_ibfk_'.time().'` FOREIGN KEY (`user_id`) REFERENCES `'.$prefix.'user` (`_id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
				
				$result = self::$_db->query($sql);
				
				if($result == false || $result->errorCode() != 00000)
					throw new Exception('error');
			
			}catch(Exception $e){
			
				throw new Exception('error create table');
			
			}
		
		}
		
		/**
			* Create category table
			*
			* @static
			* @access	private
			* @param	string	[$prefix]
		*/
		
		private static function create_category($prefix){
		
			try{
			
				$sql = 'CREATE TABLE IF NOT EXISTS `'.$prefix.'category` (
					`_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`_name` tinytext NOT NULL,
					`_type` tinytext NOT NULL,
					PRIMARY KEY (`_id`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
				
				$result = self::$_db->query($sql);
				
				if($result == false || $result->errorCode() != 00000)
					throw new Exception('error');
			
			}catch(Exception $e){
			
				throw new Exception('error create table');
			
			}
		
		}
		
		/**
			* Create comment table
			*
			* @static
			* @access	private
			* @param	string	[$prefix]
		*/
		
		private static function create_comment($prefix){
		
			try{
			
				$sql = 'CREATE TABLE IF NOT EXISTS `'.$prefix.'comment` (
					`_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`_name` tinytext NOT NULL,
					`_email` varchar(128) NOT NULL DEFAULT \'\',
					`_content` text NOT NULL,
					`_rel_id` int(11) unsigned NOT NULL,
					`_rel_type` varchar(5) NOT NULL DEFAULT \'post\',
					`_status` varchar(8) NOT NULL DEFAULT \'pending\',
					`_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (`_id`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
				
				$result = self::$_db->query($sql);
				
				if($result == false || $result->errorCode() != 00000)
					throw new Exception('error');
			
			}catch(Exception $e){
			
				throw new Exception('error create table');
			
			}
		
		}
		
		/**
			* Create link table
			*
			* @static
			* @access	private
			* @param	string	[$prefix]
		*/
		
		private static function create_link($prefix){
		
			try{
			
				$sql = 'CREATE TABLE IF NOT EXISTS `'.$prefix.'link` (
					`_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`_name` tinytext NOT NULL,
					`_link` tinytext NOT NULL,
					`_rss` tinytext,
					`_notes` text,
					`_priority` int(1) NOT NULL DEFAULT \'3\',
					PRIMARY KEY (`_id`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
				
				$result = self::$_db->query($sql);
				
				if($result == false || $result->errorCode() != 00000)
					throw new Exception('error');
			
			}catch(Exception $e){
			
				throw new Exception('error create table');
			
			}
		
		}
		
		/**
			* Create media table
			*
			* @static
			* @access	private
			* @param	string	[$prefix]
		*/
		
		private static function create_media($prefix){
		
			try{
			
				$sql = 'CREATE TABLE IF NOT EXISTS `'.$prefix.'media` (
					`_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`_name` tinytext NOT NULL,
					`_type` varchar(10) NOT NULL DEFAULT \'\',
					`_user` int(11) unsigned NOT NULL,
					`_status` varchar(7) NOT NULL DEFAULT \'draft\',
					`_category` tinytext,
					`_allow_comment` varchar(6) NOT NULL DEFAULT \'closed\',
					`_permalink` tinytext NOT NULL,
					`_embed_code` text,
					`_description` text,
					`_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					`_attachment` int(11) unsigned DEFAULT NULL,
					`_attach_type` tinytext,
					`_extra` text COMMENT \'data formatted in json\',
					PRIMARY KEY (`_id`),
					KEY `idx_user_id` (`_user`),
					CONSTRAINT `dev_media_ibfk_'.time().'` FOREIGN KEY (`_user`) REFERENCES `'.$prefix.'user` (`_id`) ON DELETE CASCADE
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
				
				$result = self::$_db->query($sql);
				
				if($result == false || $result->errorCode() != 00000)
					throw new Exception('error');
			
			}catch(Exception $e){
			
				throw new Exception('error create table');
			
			}
		
		}
		
		/**
			* Create post table
			*
			* @static
			* @access	private
			* @param	string	[$prefix]
		*/
		
		private static function create_post($prefix){
		
			try{
			
				$sql = 'CREATE TABLE IF NOT EXISTS `'.$prefix.'post` (
					`_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`_title` tinytext NOT NULL,
					`_content` text NOT NULL,
					`_allow_comment` varchar(6) NOT NULL DEFAULT \'closed\',
					`_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					`_user` int(11) unsigned NOT NULL,
					`_status` varchar(7) NOT NULL DEFAULT \'draft\',
					`_category` tinytext NOT NULL,
					`_tags` tinytext NOT NULL,
					`_permalink` tinytext NOT NULL,
					`_updated` tinyint(1) NOT NULL DEFAULT \'0\',
					`_update_user` int(11) unsigned DEFAULT NULL,
					`_extra` text COMMENT \'data formatted in json\',
					PRIMARY KEY (`_id`),
					KEY `idx_user_id` (`_user`),
					CONSTRAINT `dev_post_ibfk_'.time().'` FOREIGN KEY (`_user`) REFERENCES `'.$prefix.'user` (`_id`) ON DELETE CASCADE
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
				
				$result = self::$_db->query($sql);
				
				if($result == false || $result->errorCode() != 00000)
					throw new Exception('error');
			
			}catch(Exception $e){
			
				throw new Exception('error create table');
			
			}
		
		}
		
		/**
			* Create session table
			*
			* @static
			* @access	private
			* @param	string	[$prefix]
		*/
		
		private static function create_session($prefix){
		
			try{
			
				$sql = 'CREATE TABLE IF NOT EXISTS `'.$prefix.'session` (
					`_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`_user` int(11) unsigned NOT NULL,
					`_token` text NOT NULL,
					`_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					`_ip` text NOT NULL,
					PRIMARY KEY (`_id`),
					KEY `idx_user_id` (`_user`),
					CONSTRAINT `dev_session_ibfk_'.time().'` FOREIGN KEY (`_user`) REFERENCES `'.$prefix.'user` (`_id`) ON DELETE CASCADE
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
				
				$result = self::$_db->query($sql);
				
				if($result == false || $result->errorCode() != 00000)
					throw new Exception('error');
			
			}catch(Exception $e){
			
				throw new Exception('error create table');
			
			}
		
		}
		
		/**
			* Create setting table
			*
			* @static
			* @access	private
			* @param	string	[$prefix]
		*/
		
		private static function create_setting($prefix){
		
			try{
			
				$sql = 'CREATE TABLE IF NOT EXISTS `'.$prefix.'setting` (
					`_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`_name` text,
					`_type` tinytext NOT NULL,
					`_data` text NOT NULL COMMENT \'data formatted in json\',
					`_key` tinytext,
					PRIMARY KEY (`_id`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
				
				$result = self::$_db->query($sql);
				
				if($result == false || $result->errorCode() != 00000)
					throw new Exception('error');
			
			}catch(Exception $e){
			
				throw new Exception('error create table');
			
			}
		
		}
		
		/**
			* Test if we can connect to the database
			*
			* @static
			* @access	public
			* @return	boolean
		*/
		
		public static function installed(){
		
			try{
			
				require_once PATH.'config.php';
				
				if(!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PWD') || !defined('DB_PREFIX'))
					throw new Exception('missing constants');
				
				self::test_connection(DB_HOST, DB_NAME, DB_USER, DB_PWD);
				
				$result = self::$_db->query('SELECT COUNT(*) FROM '.DB_PREFIX.'user');
				
				if(empty($result) || $result->errorCode() != 00000)
					throw new Exception('missing table');
				
				return true;
			
			}catch(Exception $e){
			
				return false;
			
			}
		
		}
		
		/**
			* Fill database with mandatory informations
			*
			* @static
			* @access	public
			* @param	string	[$username] Admin username
			* @param	string	[$password] Admin password
			* @param	string	[$email]
		*/
		
		public static function fill($username, $password, $email){
		
			try{
			
				$user = new User();
				$user->_username = $username;
				$user->_publicname = $username;
				$user->_password = $password;
				$user->_email = $email;
				$user->_role = 'admin';
				
				$user->create();
				
				$cat = new Category();
				$cat->_type = 'album';
				$cat->_name = 'Uncategorized';
				$cat->create();
				
				$cat->_type = 'video';
				$cat->create();
				
				$cat->_type = 'post';
				$cat->create();
				
				$setting = new Setting();
				$setting->_name = 'admin';
				$setting->_type = 'role';
				$setting->_data = json_encode(array(
					'dashboard' => true, 
					'post' => true, 
					'media' => true,
					'album' => true,
					'comment' => true,
					'setting' => true,
					'delete' => true
				));
				$setting->_key = 'role_admin';
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'Homepage';
				$setting->_type = 'homepage';
				$setting->_data = json_encode(array('type' => 'post', 'view' => 'all'));
				$setting->_key = 'homepage';
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'Main Template';
				$setting->_type = 'template';
				$setting->_data = json_encode(array(
					'name' => 'Main Template',
					'author' => 'Baptiste Langlade',
					'url' => 'http://lynxpress.org',
					'infos' => array(
						'namespace' => 'main',
						'date' => '2012-08-25',
						'version' => '1.0',
						'description' => 'Lynxpress default template',
						'compatibility' => array('2.0')
					),
					'files' => array(
						'js' => array(
							'index.html',
							'viewModel.comment.js',
							'viewModel.contact.js',
							'viewModel.search.js',
							'viewModel.video.js',
							'viewModel.videos.js'
						),
						'css' => array(
							'index.html',
							'main.css'
						),
						'core' => array(
							'class.main.inc.php',
							'files/404.php',
							'files/footer.php',
							'files/header.php',
							'files/index.html',
							'html/albums/class.category.view.php',
							'html/albums/class.home.view.php',
							'html/albums/class.view.view.php',
							'html/albums/index.html',
							'html/index.html',
							'html/links/class.home.view.php',
							'html/links/index.html',
							'html/master/class.master.view.php',
							'html/master/index.html',
							'html/posts/class.category.view.php',
							'html/posts/class.home.view.php',
							'html/posts/class.tags.view.php',
							'html/posts/class.view.view.php',
							'html/posts/index.html',
							'html/videos/class.category.view.php',
							'html/videos/class.home.view.php',
							'html/videos/class.view.view.php',
							'html/videos/index.html',
							'index.html'
						)
					)
				));
				$setting->_key = 'template_main';
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'Main Template';
				$setting->_type = 'current_template';
				$setting->_data = 'main';
				$setting->_key = 'current_template';
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'Social buttons';
				$setting->_type = 'social_buttons';
				$setting->_data = json_encode(array());
				$setting->_key = 'social_buttons';
				$setting->create();
				
				$setting = new Setting();
				$setting->_name = 'Menu';
				$setting->_type = 'menu';
				$setting->_data = json_encode(array(
					array(
						'namespace' => 'posts',
						'text' => 'Posts'
					),
					array(
						'namespace' => 'albums',
						'text' => 'Albums'
					),
					array(
						'namespace' => 'videos',
						'text' => 'Videos'
					),
					array(
						'namespace' => 'links',
						'text' => 'Links'
					)
				));
				$setting->_key = 'menu';
				$setting->create();
				
				$post = new Post();
				$post->_title = 'Hello World!';
				$post->_content = 'Hi! I\'m your first post, you can modify me or just delete me and start blogging.';
				$post->_allow_comment = 'open';
				$post->_user = $user->_id;
				$post->_status = 'publish';
				$post->_category = json_encode(array("{$cat->_id}"));
				$post->_tags = 'hello, world';
				$post->_permalink = 'hello-world';
				$post->_extra = json_encode(array('banner' => null, 'gallery' => null));
				$post->create();
			
			}catch(Exception $e){
			
				throw new Exception('error fill');
			
			}
		
		}
	
	}

?>