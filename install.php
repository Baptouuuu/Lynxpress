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
	
	use \Library\Variable\Get;
	use \Site\Master\Helpers\Cache;
	use \Site\Master\Helpers\Session;
	
	define('PATH', '');
	define('SIDE', 'site');
	define('FOOTPRINT', true);
	
	try{
	
		require_once PATH.'library/loader/class.loader.lib.php';
		
		\Library\Loader\Loader::load();
		
		$controller = '\\Site\\'.ucfirst(Get::ns('install')).'\\Controllers\\'.ucfirst(Get::ctl('home'));
		
		$page = new $controller();
		
		if($page->_display_html === true)
			require_once $page->_header;
		
		$page->display_content();
		
		if($page->_display_html === true)
			require_once $page->_footer;
	
	}catch(Exception $e){
	
		\Site\Master\Helpers\Document::e404($e->getMessage(), __FILE__, __LINE__);
	
	}

?>