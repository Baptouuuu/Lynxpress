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
	use \Library\Variable\Cookie as VCookie;
	use \Library\Variable\Session as VSession;
	use \Library\Variable\Server as VServer;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Regroup methods that determine some informations about the user
		*
		* @package		Site
		* @subpackage	Master\Controllers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Session{
	
		/**
			* Init session and calls methods to determine informations
			*
			* @static
			* @access	public
		*/
		
		public static function init(){
		
			session_start();
			
			if(VCookie::lynxpress()){
			
				$cookie = json_decode(stripslashes(VCookie::lynxpress()));
				
				$_SESSION['html5'] = $cookie->html5;
				$_SESSION['renderer'] = $cookie->renderer;
			
			}elseif(!VSession::html5() && !VSession::renderer()){
			
				self::check_browser();
			
			}
		
		}
		
		/**
			* Method to get informations about the browser and stock them into session variables and in a cookie
			*
			* @static
			* @access private
		*/
		
		private static function check_browser(){
		
			$iphone = strpos(VServer::HTTP_USER_AGENT(), 'iPhone;');
			$android = strpos(VServer::HTTP_USER_AGENT(), 'Android');
			$ipad = strpos(VServer::HTTP_USER_AGENT(), 'iPad');
			$webkit = strpos(VServer::HTTP_USER_AGENT(), 'AppleWebKit/');
			$gecko = strpos(VServer::HTTP_USER_AGENT(), 'Firefox/');
			$presto = strpos(VServer::HTTP_USER_AGENT(), 'Presto/');
			$trident = strpos(VServer::HTTP_USER_AGENT(), 'Trident/'); 
			
			if($iphone !== false || $android !== false || $ipad !== false){
				
				$renderer = 'mobile';
				$html5 = true;
				
			}else{
				
				if($webkit !== false){
					
					$webkit_version = substr(VServer::HTTP_USER_AGENT(), $webkit, 20);
					
					if($webkit_version >=  'AppleWebKit/533.18.1')
						$html5 = true;
					else
						$html5 = false;
					
					$renderer = 'webkit';
				
				}elseif($gecko !== false){
				
					$gecko_version = substr(VServer::HTTP_USER_AGENT(), $gecko, 9);
					$ff10up = substr(VServer::HTTP_USER_AGENT(), $gecko, 10);
					
					if($gecko_version >= 'Firefox/4')
						$html5 = true;
					elseif($ff10up >= 'Firefox/10')
						$html5 = true;
					else
						$html5 = false;
					
					$renderer = 'gecko';
				
				}elseif($presto !== false){
				
					$html5 = true;
					$renderer = 'presto';
				
				}elseif($trident !== false){
				
					$html5 = false;
					$renderer = 'trident';
				
				}else{
				
					$html5 = false;
					$renderer = 'unknown';
				
				}
			}
			
			$_SESSION['html5'] = $html5;
			$_SESSION['renderer'] = $renderer;
			
			setcookie('lynxpress', json_encode(array('html5' => $html5, 'renderer' => $renderer)), time()+(365*24*3600));
		
		}
	
	}

?>