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
	
	namespace Site\Master\Helpers;
	use Exception;
	use \Library\Model\Setting;
	use \Library\Variable\Get as VGet;
	use \Library\Mail\Mail;
	use \Library\Variable\Server as VServer;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Contains methods related to html page (such as the menu)
		*
		* @package		Site
		* @subpackage	Master\Helpers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Document{
	
		/**
			* Retrieve the menu items list
			*
			* @static
			* @access	public
			* @return	array
		*/
		
		public static function menu(){
		
			try{
			
				$menu = new Setting('menu', '_key');
				
				$menu = json_decode($menu->_data);
				
				if(!empty($menu))
					return $menu;
				else
					return array();
			
			}catch(Exception $e){
			
				return array();
			
			}
		
		}
		
		/**
			* Method to determine page number and associated limit for sql queries
			*
			* @static
			* @access	public
			* @param	integer [$items] Items number per page
			* @return	array
		*/
		
		public static function pagination($items){
		
			if(!VGet::p()){
			
				$limit_start = 0;
				$page = 1;
			
			}else{
			
				if(VGet::p() < 1)
					$page = 1;
				else
					$page = VGet::p();
				
				$limit_start = ($page - 1) * $items;
			
			}
			
			return array($page, $limit_start);
		
		}
		
		/**
			* Method that handle 404 http errors
			*
			* @static
			* @access	public
			* @param	string	[$message]
			* @param	string	[$file]
			* @param	integer	[$line]
		*/
		
		public static function e404($message, $file, $line){
		
			$mail = new Mail(WS_EMAIL, 'HTTP 404 reached');
			
			$mail->_message = 'Lynxpress says: "'.$message.'" (called from "'.$file.'" on line '.$line.')'."\n".
							  'server variables:'."\n".str_replace('",', "\",\n",json_encode(VServer::all()));
			$mail->send();
			
			header('Location: '.WS_URL.'404.php');
			exit;
		
		}
	
	}

?>