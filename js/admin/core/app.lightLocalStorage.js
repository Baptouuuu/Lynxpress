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

var App = App || {};

/**
	* To run localStorage with your form, first you need to add this file in your page (obvious :))
	* The for each element you want to store add the class 'storage' and add the attribute 'data-storage' to your element, its value will represent the key in the localStorage object
	* You can add a button with classes 'button' and 'clear' to allow user to erase localStorage of current form inputs
	*
	* @package	App
*/

$(document).ready(function() {

	App.lightLocalStorage = {
	
		to_store: $('.storage'),
		clear_button: $('.button.clear'), 
		
		init: function() {
		
			if(localStorage !== undefined){
			
				this.clear_button.show();
				this.to_store.on('change', this.store);
				
				this.load_data();
				
				$('form').on('submit', this.clear);
				this.clear_button.on('click', this.clear);
			
			}else{
			
				console.log('This browser do not support localStorage');
			
			}
		
		},
		
		load_data: function() {
		
			$.each(App.lightLocalStorage.to_store, function() {
				
				var $this = $(this),
					name = 'lp_'+$this.data('storage');
				
				if(localStorage.getItem(name) && localStorage.getItem(name) != $this.val()){
				
					$this.val(localStorage.getItem(name));
					
					$this.addClass('from_localstorage');
				
				}
				
			});
		
		},
		
		store: function() {
		
			var $this = $(this);
			
			localStorage.setItem('lp_'+$this.data('storage'), $this.val());
		
		},
		
		clear: function() {
		
			$.each(App.lightLocalStorage.to_store, function() {
			
				var $this = $(this),
					name = 'lp_'+$this.data('storage');
				
				localStorage.removeItem(name);
				$this.removeClass('from_localstorage');
			
			});
		
		}
	
	};
	
	App.lightLocalStorage.init();

});