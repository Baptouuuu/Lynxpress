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
	* Used with \Admin\Media\Controllers\Edit
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	/**
		* Makes a backup of non submitted data from the user
	*/
	
	ViewModels.storage = {
	
		id: $('#media_id').val(),
		name: $('#em_name'),
		description: $('#em_desc'),
		embed_code: $('#em_embed'),
		object: null,
		resetButton: $('.button.clear'),
		form: $(document.forms[0]),
		
		init: function() {
		
			this.load();
			
			this.name.on('change', this.save);
			this.description.on('change', this.save);
			this.embed_code.on('change', this.save);
			
			this.resetButton.on('click', this.delete);
			this.form.on('submit', this.delete);
		
		},
		
		/**
			* Load data from localStorage, and if the data is different we replace it in the DOM
		*/
		
		load: function() {
		
			this.object = new Models.Media(this.id);
			
			if(this.object.exist()){
			
				if(this.object.name !== '' && this.object.name !== this.name.val()){
				
					this.name.val(this.object.name);
					this.name.addClass('from_localstorage');
					
					this.resetButton.show();
				
				}
				
				if(this.object.description !== '' && this.object.description !== this.description.val()){
				
					this.description.val(this.object.description);
					this.description.addClass('from_localstorage');
					
					this.resetButton.show();
				
				}
				
				if(this.object.embed_code !== '' && this.object.embed_code !== this.embed_code.val()){
				
					this.embed_code.val(this.object.embed_code);
					this.embed_code.addClass('from_localstorage');
					
					this.resetButton.show();
				
				}
			
			}
		
		},
		
		/**
			* Update localStorage when a modification is made
		*/
		
		save: function(event) {
		
			var property = $(this).attr('name');
			
			ViewModels.storage.object[property] = ViewModels.storage[property].val();
			
			if(ViewModels.storage.object.exist())
				ViewModels.storage.object.update(property);
			else
				ViewModels.storage.object.create();
		
		},
		
		delete: function(event) {
		
			ViewModels.storage.name.removeClass('from_localstorage');
			ViewModels.storage.description.removeClass('from_localstorage');
			ViewModels.storage.embed_code.removeClass('from_localstorage');
			
			ViewModels.storage.object.delete();
			
			ViewModels.storage.resetButton.hide();
		
		}
	
	};
	
	ViewModels.storage.init();

});