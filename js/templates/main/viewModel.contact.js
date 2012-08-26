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
	* Allows the user to post a mail asynchronously to the server
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	ViewModels.contact = {
	
		toggle: $('#fcontact'),
		block: $('#contact'),
		form: null,
		bg: null,
		server: new App.Server,
		email: null,
		object: null,
		content: null,
		button: null,
		
		init: function() {
		
			this.form = this.block.children('#contact_form');
			this.bg = this.block.children('#contact_bg');
			this.email = this.block.find('input[name="cemail"]');
			this.object = this.block.find('input[name="cobject"]');
			this.content = this.block.find('textarea[name="ccontent"]');
			this.button = this.block.find('button');
			
			this.server.parameters.create = {
				url: this.form.data('url'),
				method: 'post',
				callback: this.sent
			};
			
			this.toggle.on('click', this.display);
			this.bg.on('click', this.hide);
			this.button.on('click', this.send);
		
		},
		
		send: function(event) {
		
			var self = ViewModels.contact;
			
			event.preventDefault();
			
			if(self.checkValidity()){
			
				self.server.create({
					email: self.email.val(),
					object: self.object.val(),
					content: self.content.val(),
					create: true
				});
				
				self.form.prepend('<div class="message good">Sending your message...</div>');
			
			}
		
		},
		
		sent: function(event) {
		
			var self = ViewModels.contact,
				resp = self.server.listen(event);
			
			if(resp !== false){
				
				var msg = self.form.find('.message.good');
				
				if(resp.message === undefined){
				
					msg.removeClass('good').addClass('wrong').text('An error occured');
				
				}else if(resp.message !== true){
				
					msg.removeClass('good').addClass('wrong').text('An error occured');
					console.log(resp.message);
				
				}else{
				
					msg.text('Mail sent!');
					self.content.val('');
				
				}
			
			}
		
		},
		
		checkValidity: function() {
		
			var self = ViewModels.contact,
				pass = true;
			
			if(!self.email[0].checkValidity()){
			
				self.email.addClass('error');
				pass = false;
			
			}else{
			
				self.email.removeClass('error');
			
			}
			
			if(!self.object[0].checkValidity()){
			
				self.object.addClass('error');
				pass = false;
			
			}else{
			
				self.object.removeClass('error');
			
			}
			
			if(!self.content[0].checkValidity()){
			
				self.content.addClass('error');
				pass = false;
			
			}else{
			
				self.content.removeClass('error');
			
			}
			
			return pass;
		
		},
		
		display: function(event) {
		
			var self = ViewModels.contact;
			
			event.preventDefault();
			
			self.block.show(0, function() {
			
				self.bg.addClass('show');
				self.form.addClass('show');
			
			});
		
		},
		
		hide: function(event) {
		
			var self = ViewModels.contact;
			
			event.preventDefault();
			
			self.resetScreen();
		
		},
		
		resetScreen: function() {
		
			var self = ViewModels.contact;
			
			self.block.hide();
			
			self.bg.removeClass('show');
			self.form.removeClass('show');
		
		}
	
	};
	
	ViewModels.contact.init();

});