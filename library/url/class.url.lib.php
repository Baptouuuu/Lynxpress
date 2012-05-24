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
	
	namespace Library\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Allows to easily build url depending on wether the url rewriting is activated or not
		* Example:
		* <code>
		* 	//URL_REWRITING is set to true and call is made in the admin
		* 	echo Url::_(array('ns' => 'media', 'ctl' => 'manage'), array('action' => 'edit', 'id' => '2'), false);
		*	//will output: http://mysite.com/admin/media/manage?action=edit&id=2
		*
		*	//URL_REWRITING is set to false and call is made in the site
		*	echo Url::_(array('ns' => 'media', 'ctl' => 'manage'), array('action' => 'edit', 'id' => '2'), true);
		*	//will output: http://mysite.com/admin/?ns=media&ctl=manage&action=edit&id=2
		* </code>
		* Make sure that corresponding key/value pairs rewrite rules exists in the .htaccess
		* Otherwise the server will not understand your request
		*
		* @package		Library
		* @subpackage	Url
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
	*/
	
	class Url{
	
		/**
			* Method to build your urls, see class description to understand how it works
			* Both arrays parameters have to be built as follows:
			* <code>
			* 	array[
			*		'GET parameter' => 'parameter value'
			*	];
			* </code>
			* 
			* @static
			* @access	public
			* @param	array	[$to_rewrite] Parameters value will be concatenated with a '/'
			* @param	array	[$query_string] Additional GET parameters if there's no specific rule in the htaccess
			* @param	boolean	[$other_side] If set to true and SIDE is to admin, we will target site side and vice versa
			* @return	string
		*/
		
		public static function _(array $to_rewrite, array $query_string = array(), $other_side = false){
		
			$to_return = WS_URL;
			
			if(($other_side === false && SIDE == 'admin') || ($other_side === true && SIDE == 'site'))
				$to_return .= 'admin/';
			
			if(defined('URL_REWRITING') && URL_REWRITING === true){
			
				$to_return .= implode('/', $to_rewrite);
				
				if(!empty($query_string)){
				
					$to_return .= '?';
					
					foreach($query_string as $key => &$s)
						$s = $key.'='.$s;
					
					$to_return .= implode('&', $query_string);
				
				}
			
			}else{
			
				$to_return .= '?';
				
				foreach($to_rewrite as $key => &$v)
					$v = $key.'='.$v;
				
				$to_return .= implode('&', $to_rewrite);
				
				if(!empty($query_string)){
				
					$to_return .= '&';
					
					foreach($query_string as $key => &$v)
						$v = $key.'='.$v;
					
					$to_return .= implode('&', $query_string);
				
				}
			
			}
			
			return $to_return;
		
		}
	
	}

?>