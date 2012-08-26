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
	* Used with Admin\Network\Controllers\Post and \Album in order to post comments
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	ViewModels.comments = {
	
		block: $('#network_comments'),
		server: new App.Server,
		
		init: function() {
		
			this.server.parameters.create.url = this.block.data('url');
			this.server.parameters.create.callback = this.sent;
			
			this.block.on('click', this.send);
		
		},
		
		send: function(event) {
		
			var t = event.originalEvent.target;
			
			if(t.classList.contains('button') && t.classList.contains('publish')){
			
				var content = ViewModels.comments.block.find('#form > textarea');
				
				ViewModels.comments.server.create({
					type:		ViewModels.comments.block.data('type'),
					id:			ViewModels.comments.block.data('id'),
					website:	ViewModels.comments.block.data('ws'),
					content:	content.val()
				});
				
				content.val('');
			
			}
		
		},
		
		sent: function(e) {
		
			var resp = ViewModels.comments.server.listen(e);
			
			if(resp !== false){
				
				var menu = $('#menu');
			
				(resp.message) ? menu.after(resp.message) : menu.after(resp);
				ViewModels.messages.recheck();
			
			}
		
		}
	
	};
	
	ViewModels.comments.init();

});