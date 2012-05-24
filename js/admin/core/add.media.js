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

//used with Admin\Media\Controllers\Add

$(document).ready(function() {

	upload = {
	
		form: $('#upload_form'),
		form_url: null,
		dropzone: null,
		input: null,
		button: null,
		files_list: null,
		files: null,
		xhrs: null,
		responses: null,
		menu: null,
		
		init: function() {
		
			//initialize attributes
			this.form_url = this.form.data('url');
			this.dropzone = this.form.find('#dropzone');
			this.input = this.form.find('#file');
			this.button = this.form.find('#upload');
			this.files_list = this.form.find('#files_list');
			this.menu = $('#menu');
			this.files = [];
			this.xhrs = [];
			this.responses = [];
			
			//show dropzone because js is enabled
			this.dropzone.show();
			
			//attach listener to input and dropzone
			this.input.on('change', this.load_data_from_input);
			this.dropzone.on('dragenter', this.ignore_drag);
			this.dropzone.on('dragover', this.ignore_drag);
			this.dropzone.on('drop', this.load_data_from_drop);
			this.dropzone.on('dragend', this.remove_hover_drop);
			this.dropzone.on('dragleave', this.remove_hover_drop);
		
		},
		
		ignore_drag: function(event) {
		
			event.originalEvent.stopPropagation();
			event.originalEvent.preventDefault();
			
			upload.dropzone.addClass('hovered');
		
		},
		
		load_data_from_input: function() {
		
			if(this.files.length > 0)
				for(var i = 0; i < this.files.length; i++)
					upload.files.push(this.files[i]);
			
			upload.start()
		
		},
		
		load_data_from_drop: function(event) {
		
			upload.ignore_drag(event);
			
			if(event.originalEvent.dataTransfer.files.length > 0)
				for(var i = 0; i < event.originalEvent.dataTransfer.files.length; i++)
					upload.files.push(event.originalEvent.dataTransfer.files[i]);
					
			upload.remove_hover_drop();
			
			upload.start();
		
		},
		
		remove_hover_drop: function() {
		
			upload.dropzone.removeClass('hovered');
		
		},
		
		start: function() {
		
			if(upload.files.length > 0){
			
				for(var i = 0; i < upload.files.length; i++){
				
					var in_list = upload.files_list.find('#item_'+i);
					
					if(in_list.length === 0){
					
						upload.files_list.append(upload.html(i, upload.calc_filesize(upload.files[i].size)));
						
						upload.send(i);
					
					}
				
				}
			
			}
		
		},
		
		calc_filesize: function(bits) {
		
			if(bits > 1024 * 1024)
			  return (Math.round(bits * 100 / (1024 * 1024)) / 100).toString() + 'MB';
			else
			  return (Math.round(bits * 100 / 1024) / 100).toString() + 'KB';
		
		},
		
		html: function(id, size) {
		
			var html = null;
			
			html = '<li id="item_'+id+'">';
			html += '<div class="fl_image"></div>';
			html += '<div class="fl_name">'+upload.files[id].name+' ('+size+')</div>';
			html += '<div class="fl_progress"><div class=progress><div class=bar></div></div></div>';
			html += '<div class="fl_link"></div>';
			html += '</li>';
			
			return html;
		
		},
		
		send: function(id) {
		
			var data = new FormData();
			
			upload.xhrs[id] = new XMLHttpRequest();
			
			data.append('upload', 'Upload');
			data.append('file', upload.files[id]);
			
			upload.xhrs[id].upload.addEventListener("progress", function(event) {upload.upload_progress(event, id)}, false);
			upload.xhrs[id].onreadystatechange = function() {upload.request_done(id)};
			
			upload.xhrs[id].open('post', upload.form_url);
			upload.xhrs[id].send(data);
		
		},
		
		upload_progress: function(event, id) {
		
			var percent = null,
				progress = upload.files_list.find('#item_'+id+' .progress > .bar');
			
			if(event.lengthComputable){
			
				percent = Math.round(event.loaded * 100 / event.total);
				
				progress.css('width', percent+'%');
				//progress.text('Progress: '+percent.toString()+'%');
			
			}else{
			
				alert('error');
			
			}
		
		},
		
		request_done: function(id) {
		
			if(upload.xhrs[id].readyState == 4 && (upload.xhrs[id].status == 200 || upload.xhrs[id].status == 0)){
			
				var li = upload.files_list.find('#item_'+id),
					img = null,
					link = null;
				console.log();
				try{
				
					upload.responses[id] = JSON.parse(upload.xhrs[id].response);
				
				}catch(e){
				
					upload.responses[id].message = 'A server error occured';
				
				}
				
				if(upload.responses[id].message === true){
				
					if(upload.responses[id].infos[0].type.substring(0, 5) == 'image')
						img = '<a class="fancybox" href="'+upload.responses[id].infos[0].path+'"><img src="'+upload.responses[id].infos[0].thumb150+'" alt="" /></a>';
					else if(upload.responses[id].infos[0].type.substring(0, 5) == 'video')
						img = '<img src="'+upload.responses[id].infos[0].path+'" alt="video" />';
					
					link = '<a href="'+upload.responses[id].infos[0].edit_url+'">'+upload.responses[id].infos[0].edit_word+'</a>';
					
					li.find('.fl_progress').hide();
					li.find('.fl_image').html(img);
					li.find('.fl_link').show().html(link);
				
				}else{
				
					upload.menu.after(upload.responses[id].message);
					li.remove();
					upload.files.splice(id, 1);
					upload.xhrs.splice(id, 1);
					upload.responses.splice(id, 1);
				
				}
			
			}
		
		}
	
	};
	
	upload.init();

});