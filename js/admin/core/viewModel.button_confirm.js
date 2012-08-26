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
	* Usable on elements with "button" class and "data-confirm" attribute
	* If button clicked a "confirm" class is added to the button if you want to change the design
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	ViewModels.button_confirm = {
	
		buttons: $('.button[data-confirm]'),
		
		init: function() {
		
			this.load();
			
			this.buttons.on('click', this.ask);
			$(document.body).on('click', this.reset);
		
		},
		
		load: function() {
		
			$.each(this.buttons, function() {
			
				var $this = $(this);
				
				$this.data('initial', $this.val());
			
			});
		
		},
		
		ask: function(event) {
		
			var t = event.originalEvent.target,
				$this = $(this);
			
			if(!(t.classList.contains('confirm'))){
			
				event.preventDefault;
				
				ViewModels.button_confirm.reset();
				
				$this.val($this.data('confirm'));
				
				t.classList.add('confirm');
				
				return false;
			
			}
		
		},
		
		reset: function() {
		
			$.each(ViewModels.button_confirm.buttons, function() {
			
				var $this = $(this);
				
				this.classList.remove('confirm');
				
				$this.val($this.data('initial'));
			
			});
		
		}
	
	};
	
	ViewModels.button_confirm.init();

});