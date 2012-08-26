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
	* Allow a user to post a comment to the website
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	ViewModels.comment = {
	
		block: $('#cform'),
		server: new App.Server,
		localStorage: new App.localStorage,
		form: null,
		name: null,
		email: null,
		content: null,
		
		init: function() {
		
			this.server.parameters.create.url = this.block.data('url');
			this.server.parameters.create.callback = this.sent;
			
			this.form = this.block.parent();
			this.name = this.block.find('.input[name="name"]'),
			this.email = this.block.find('.input[name="email"]'),
			this.content = this.block.find('textarea');
			
			this.initData();
			
			this.form.on('submit', function(event) { event.preventDefault(); });
			this.block.on('click', this.send);
		
		},
		
		initData: function() {
		
			var user = this.localStorage.read('user');
			
			if(user === null || user === false){
			
				this.localStorage.create('user', {
					name: '',
					email: ''
				});
			
			}else{
			
				this.name.val(user.name);
				this.email.val(user.email);
			
			}
		
		},
		
		send: function(event) {
		
			var self = ViewModels.comment,
				t = event.originalEvent.target;
			
			if(t.nodeName == 'BUTTON' && self.name.val() !== '' && self.email.val() !== '' && self.content.val() !== ''){
			
				self.server.create({
					name:		self.name.val(),
					email:		self.email.val(),
					content:	self.content.val(),
					id:			self.block.data('id'),
					type:		self.block.data('type'),
					create:		true
				});
				
				self.block.prepend('<div class="message good">Sending your comment...</div>');
				
				self.localStorage.update('user', self.name.val(), 'name');
				self.localStorage.update('user', self.email.val(), 'email');
			
			}
		
		},
		
		sent: function(e) {
		
			var self = ViewModels.comment,
				resp = self.server.listen(e);
			
			if(resp !== false){
				
				var msg = self.block.find('.message.good');
			
				if(resp.message === undefined){
				
					msg.removeClass('good').addClass('wrong').text('An error occured');
				
				}else if(resp.message !== true){
				
					msg.removeClass('good').addClass('wrong').text(resp.message);
				
				}else{
				
					msg.text('Comment sent! Waiting for approval...');
					self.content.val('');
				
				}
			
			}
		
		}
	
	};
	
	ViewModels.comment.init();

});