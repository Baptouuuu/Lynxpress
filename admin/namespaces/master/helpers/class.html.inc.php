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
	
	namespace Admin\Master\Helpers;
	use \Library\Variable\Get as VGet;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Html Helper
		*
		* @package		Admin
		* @subpackage	Master\Helpers
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Html{
	
		private static $_header_links = array('js' => array(), 'css' => array());
		
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
			* Build an html datalist from an array for search input
			*
			* @static
			* @access	public
			* @param	integer [$id] Datalist id
			* @param	array [$array] Array of objects
			* @param	string [$column] Object attribute to set in datalist
			* @return	mixed if $array is not an array it returns false, otherwise it returns html datalist
		*/
		
		public static function datalist($id, $array, $column){
		
			if(is_array($array)){
				
				$datalist = '<datalist id="'.$id.'">';
				
				foreach($array as $value)
					$datalist .= '<option value="'.$value->$column.'">';
				
				$datalist .= '</datalist>';
				
				return $datalist;
				
			}else{
			
				return false;
			
			}
		
		}
		
		/**
			* Add an elemment into static attribute $_header_links
			* This element will be added as an link attribute into html header
			*
			* @static
			* @access	public
			* @param	string	[$type] Can be set to 'js' or 'css'
			* @param	string	[$data] Link or code you want to add to html header
			* @param	boolean	[$src] If set to true it's a link, otherwise $data is considered as plain text
			* @param	string	[$extra] For css it will be the media attribute, and for the js it will be the loading method ('async' or 'defer')
		*/
		
		public static function add_header_link($type, $data, $src = true, $extra = ''){
		
			self::$_header_links[$type][] = array('data' => $data, 'src' => $src, 'extra' => $extra);
		
		}
		
		/**
			* Display header links added by user current controller
			*
			* @static
			* @access	public
			* @param	string	[$type] Type to display
		*/
		
		public static function extend_document($type){
		
			if($type == 'css'){
			
				foreach(self::$_header_links['css'] as $css){
				
					if($css['src'] == true)
						echo '<link rel="stylesheet" type="text/css" href="'.$css['data'].'" '.((!empty($css['extra']))?'media="'.$css['extra'].'"':'').'>';
					else
						echo '<style>'.$css['data'].'</style>';
				
				}
			
			}elseif($type == 'js'){
				
				foreach(self::$_header_links['js'] as $js){
				
					if($js['src'] == true)
						echo '<script src="'.$js['data'].'" type="text/javascript" '.((!empty($js['extra']))?$js['extra']:'').'></script>';
					else
						echo '<script type="text/javascript">'.$js['data'].'</script>';
				
				}
			
			}
		
		}
	
	}

?>