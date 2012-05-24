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

//used with Admin\Dashboard\Controllers\Manage

$(document).ready(function() {

	widgets = {
	
		widgets: $('.widget'),
		toggle: null,
		
		init: function() {
		
			this.toggle = this.widgets.find('.header > .toggle');
			
			this.load();
			
			this.toggle.on('click', this.toggle_widget);
		
		},
		
		toggle_widget: function(event) {
		
			var $this = $(this),
				widget = $('#'+$this.data('widget'));
			
			widget.toggleClass('closed');
			
			widgets.save();
		
		},
		
		load: function() {
		
			if(localStorage !== undefined){
			
				var from_store = localStorage.getItem('lp_dashboard_widgets');
				
				if(from_store !== null){
					$.each(JSON.parse(from_store), function() {
					
						if(this.closed){
						
							var widget = $('#'+this.id);
							
							widget.addClass('closed');
						
						}
					
					});
				}
			
			}
		
		},
		
		save: function() {
		
			if(localStorage !== undefined){
			
				var to_store = [];
				
				$.each(this.widgets, function() {
				
					var $this = $(this);
					
					to_store.push({
						'id': $this.attr('id'),
						'closed': $this.hasClass('closed')
					});
				
				});
				
				localStorage.setItem('lp_dashboard_widgets', JSON.stringify(to_store));
			
			}
		
		}
	
	};
	
	widgets.init();

});