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
	
	namespace Library\Loader;
	use Exception;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Loader
		*
		* Class to autoload php files
		*
		* Files names are determined with there namespaces
		*
		* @package		Library
		* @subpackage	Loader
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.1
		* @abstract
	*/
	
	abstract class Loader{
			
		const NS = 'namespaces/';
		
		/**
			* Load php files thanks to there namespaces
			*
			* @static
			* @access	private
		*/
		
		private static function autoloader($class){
		
			$namespaces = explode('\\', strtolower($class));
			
			if($namespaces[0] == 'library'){

				if(count($namespaces) == 4){
				
					$require = PATH.'library/'.$namespaces[1].'/'.$namespaces[2].'/';
					
					if($namespaces[2] == 'interfaces')
						$require .= 'interface';
					else
						$require .= 'class';
					
					$require .= '.'.$namespaces[3].'.lib.php';
				
				}else{
				
					$require = PATH.'library/'.$namespaces[1].'/class.'.$namespaces[2].'.lib.php';
				
				}
			
			}elseif($namespaces[0] == 'template'){
			
				$require = PATH.'template/'.$namespaces[1].'/class.'.$namespaces[2].'view.php';
						
			}else{

				$require = null;
				
				if(SIDE == 'site' && $namespaces[0] == 'admin')
					$require = 'admin/';
				elseif(SIDE == 'admin' && $namespaces[0] == 'site')
					$require = PATH;
				
				$require .= self::NS.$namespaces[1].'/';
				
				if(count($namespaces) == 4){
				
					$require .= $namespaces[2].'/';
					
					if($namespaces[2] == 'interfaces')
						$require .= 'interface';
					else
						$require .= 'class';
					
					$require .= '.'.$namespaces[3].'.';
					
					if($namespaces[2] == 'html')
						$require .= 'view';
					else
						$require .= 'inc';
					
					$require .= '.php';
				
				}else{
				
					$require .= 'class.'.$namespaces[2].'.inc.php';
				
				}
			
			}
			
			if(!file_exists($require))
				throw new Exception('Namespace "'.$class.'" doesn\'t exists');
			
			require_once $require;
			
			if(!class_exists($class) && !interface_exists($class))
				throw new Exception('Controller "'.$class.'" doesn\'t exists');
		
		}
		
		/**
			* Register method to autoload php classes files
			*
			* @static
			* @access	public
		*/
		
		public static function load(){
		
			if(!extension_loaded('spl'))
				die('SPL extension not loaded!');
		
			spl_autoload_register(null, false);
			spl_autoload_extensions('.inc.php, .view.php, .lib.php');
			spl_autoload_register('self::autoloader', true);
		
		}
	
	}

?>