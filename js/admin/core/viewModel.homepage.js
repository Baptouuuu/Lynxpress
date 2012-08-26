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

var ViewModels = ViewModels || {};

/**
	* Used with Admin\DefaultPage\Controllers\Manage
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	ViewModels.homepage = {
	
		types: $('#homepage'),
		lists: $('#homepage_lists > select'),
		
		init: function() {
		
			this.types.on('click', this.showList);
		
		},
		
		showList: function(event) {
		
			var t = event.originalEvent.target,
				$this = $(this);
			
			if(t.name === 'type'){
			
				ViewModels.homepage.lists.hide();
				
				$('#hl_'+t.value).show();
			
			}
		
		}
	
	};
	
	ViewModels.homepage.init();

});