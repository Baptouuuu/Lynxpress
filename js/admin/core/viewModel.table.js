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
	* Allow to check all elements in a table at once, like in \Admin\Posts\Controllers\Manage
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	ViewModels.table = {
	
		ca_input: $('.check_all'),
		select: null,
		inputs: null,
		
		init: function() {
		
			this.select = this.ca_input.data('select');
			this.inputs = $('.'+this.select);
			this.ca_input.on('click', this.toggle_select)
		
		},
		
		toggle_select: function(event) {
		
			if(this.checked){
			
				ViewModels.table.ca_input.attr('checked', true);
				ViewModels.table.inputs.attr('checked', true);
			
			}else{
			
				ViewModels.table.ca_input.attr('checked', false);
				ViewModels.table.inputs.attr('checked', false);
			
			}
		
		}
	
	};
	
	ViewModels.table.init();

});