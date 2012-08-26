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
	use \Library\File\File;
	use \Library\Variable\Server as VServer;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Create the configuration file
		*
		* @package		Site
		* @subpackage	Install\Helpers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Config{
	
		/**
			* Init the config file creation
			*
			* @static
			* @access	public
			* @param	string	[$db_host]
			* @param	string	[$db_name]
			* @param	string	[$db_user]
			* @param	string	[$db_pwd]
			* @param	string	[$db_prefix]
			* @param	string	[$ws_name]
			* @param	string	[$ws_url]
			* @param	string	[$ws_email]
		*/
		
		public static function create($db_host, $db_name, $db_user, $db_pwd, $db_prefix, $ws_name, $ws_url, $ws_email){
		
			try{
			
				$sample = File::read(PATH.'config.sample.php');
				
				$config = new File();
				
				$config->_content = self::build($db_host, $db_name, $db_user, $db_pwd, $db_prefix, $ws_name, $ws_url, $ws_email);
				
				$config->save(PATH.'config.php');
			
			}catch(Exception $e){
			
				if(substr($e->getMessage(), 0, 18) == 'File can\'t be read')
					throw new Exception('config sample missing');
				else
					throw new Exception('error create config');
			
			}
		
		}
		
		/**
			* Build configuration file content
			*
			* @static
			* @access	public
			* @param	string	[$db_host]
			* @param	string	[$db_name]
			* @param	string	[$db_user]
			* @param	string	[$db_pwd]
			* @param	string	[$db_prefix]
			* @param	string	[$ws_name]
			* @param	string	[$ws_url]
			* @param	string	[$ws_email]
		*/
		
		public static function build($db_host, $db_name, $db_user, $db_pwd, $db_prefix, $ws_name, $ws_url, $ws_email){
		
			try{
			
				$sample = File::read(PATH.'config.sample.php');
				
				$content = $sample->_content;
				
				$content = str_replace('define(\'WS_NAME\', \'\');', 'define(\'WS_NAME\', \''.$ws_name.'\');', $content);
				$content = str_replace('define(\'WS_URL\', \'\');', 'define(\'WS_URL\', \''.$ws_url.'\');', $content);
				$content = str_replace('define(\'WS_EMAIL\', \'\');', 'define(\'WS_EMAIL\', \''.$ws_email.'\');', $content);
				$content = str_replace('define(\'DB_HOST\', \'\');', 'define(\'DB_HOST\', \''.$db_host.'\');', $content);
				$content = str_replace('define(\'DB_NAME\', \'\');', 'define(\'DB_NAME\', \''.$db_name.'\');', $content);
				$content = str_replace('define(\'DB_USER\', \'\');', 'define(\'DB_USER\', \''.$db_user.'\');', $content);
				$content = str_replace('define(\'DB_PWD\', \'\');', 'define(\'DB_PWD\', \''.$db_pwd.'\');', $content);
				$content = str_replace('define(\'DB_PREFIX\', \'\');', 'define(\'DB_PREFIX\', \''.$db_prefix.'\');', $content);
				$content = str_replace('define(\'SALT\', \'I@mYour5ecretKey!\');', 'define(\'SALT\', \''.self::generate_key().'\');', $content);
				
				//test if rewrite engine is available
				if(function_exists('apache_get_modules')){
				
					$modules = apache_get_modules();
					$bool = (in_array('mod_rewrite', $modules)) ? true : false;
				
				}else{
				
					$bool = (VServer::HTTP_MOD_REWRITE(false) == 'On') ? true : false;
				
				}
				
				$content = str_replace('define(\'URL_REWRITING\', \'\');', 'define(\'URL_REWRITING\', \''.$bool.'\');', $content);
				
				return $content;
			
			}catch(Exception $e){
			
				return '';
			
			}
		
		}
		
		/**
			* Generate a private key
			*
			* @static
			* @access	public
			* @return	string
		*/
		
		public static function generate_key(){
		
			if(!extension_loaded('hash'))
				return 'I@mYour5ecretKey!';
			
			return base64_encode(md5(md5(uniqid().mt_rand(time().mt_rand().(time()+rand()), (time()+rand()).mt_rand().time()))));
		
		}
	
	}

?>