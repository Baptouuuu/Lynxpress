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
	
	namespace Admin\Plugins\Html;
	use \Admin\Master\Html\Master;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	use \Library\Media\Media;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for add controller
		*
		* @package		Admin
		* @subpackage	Plugins\Html
		* @author		Baptiste Langlade lynxpressorg@gmail.com
		* @version		1.0
		* @abstract
	*/
	
	abstract class Add extends Master{
	
		/**
			* Display the menu
			*
			* @static
			* @access	public
		*/
		
		public static function menu(){
		
			echo '<div id="menu">'.
					'<span id=menu_selected class=menu_item><a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'library')).'">'.Lang::_('Library').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'plugins')).'">'.Lang::_('Plugins').'</a></span>'.
					'<span class=menu_item><a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a></span>'.
				 '</div>';
		
		}
		
		/**
			* Display form to upload a plugin
			*
			* @static
			* @access	public
		*/
		
		public static function upload(){
		
			echo '<h2>'.Lang::_('Add a plugin to your website', 'plugins').'</h2>'.
				 '<div id=upload_form>'.
				 	Lang::_('Upload a plugin archive', 'templates').': <input type=file name=plugin /> <input class="button button_publish" type=submit name=create value="'.Lang::_('Upload', 'media').'" /><br/>'.
				 	'<span class=indication>'.Lang::_('The maximum upload file size is set to', 'media').' '.Media::max_upload().'MB</span>'.
				 '</div>';
		
		}
	
	}

?>