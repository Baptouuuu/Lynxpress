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
	* Used with \Admin\Media\Controllers\Add
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	/**
		* Container for all files uploaded
	*/
	
	ViewModels.files = {
	
		list: []
	
	};
	
	/**
		* Process file upload via AJAX
	*/
	
	ViewModels.upload = {
	
		menu: $('#menu'),
		section: $('#upload_form'),
		dropzone: null,
		fileInput: null,
		filesList: null,
		
		init: function() {
		
			//check if browser support drag&drop
			if(('draggable' in document.body) || ('ondragstart' in document.body && 'ondrop' in document.body)){
			
				this.dropzone = this.section.children('#dropzone');
				this.dropzone.show();
				
				this.fileInput = this.section.children('#file');
				this.filesList = this.section.children('#files_list');
				
				//we extend App.Server to listen to events correctly
				this.extendAppServer();
				
				this.fileInput.on('change', this.addInputFiles);
				
				this.dropzone.on('dragenter', this.ignoreDrag);
				this.dropzone.on('dragover', this.ignoreDrag);
				this.dropzone.on('drop', this.addDropFiles);
				this.dropzone.on('dragend', this.removeHover);
				this.dropzone.on('dragleave', this.removeHover);
			
			}else{
			
				console.log('Your browser do not support the drag n\' drop feature');
			
			}
		
		},
		
		/**
			* Handle adding files when input has changed
		*/
		
		addInputFiles: function(event) {
		
			if(this.files.length > 0)
				for(var i = 0; i < this.files.length; i++)
					ViewModels.upload.initFile(
						this.files[i]
					);
			
			ViewModels.upload.start();
		
		},
		
		/**
			* Tells the browser to not behave as usual if the dropzone is hovered with files
		*/
		
		ignoreDrag: function(event) {
		
			event.originalEvent.stopPropagation();
			event.originalEvent.preventDefault();
			
			ViewModels.upload.dropzone.addClass('hovered');
		
		},
		
		/**
			* Remove effect on the dropzone when the mouse leaves it
		*/
		
		removeHover: function(event) {
		
			ViewModels.upload.dropzone.removeClass('hovered');
		
		},
		
		/**
			* Handle adding files when some are dropped in the dropzone
		*/
		
		addDropFiles: function(event) {
		
			ViewModels.upload.ignoreDrag(event);
			
			var files = event.originalEvent.dataTransfer.files;
			
			if(files.length > 0)
				for(var i = 0; i < files.length; i++)
					ViewModels.upload.initFile(
						files[i]
					);
			
			ViewModels.upload.removeHover();
			
			ViewModels.upload.start();
		
		},
		
		/**
			* Add a file object to the list of files and add an element in the files list
		*/
		
		initFile: function(file) {
		
			var obj = {
					id: 0,
					name: file.name,
					size: file.size,
					type: file.type,
					data: file,
					path: '',
					thumb150: '',
					thumb300: '',
					thumb1000: '',
					editUrl: '',
					editWord: '',
					uploaded: false,
					inProgress: false,
					element: null,
					server: new App.Server({
						create: {
							url: this.section.data('url'),
							method: 'post'
						}
					})
				};
			
			ViewModels.files.list.push(obj);
			
			var listId = (ViewModels.files.list.length - 1);
			
			this.filesList.prepend(Views.files.makeMedia(
				listId,
				obj.name,
				obj.size
			));
			
			ViewModels.files.list[listId].element = ViewModels.upload.section.find('#item_'+listId);
		
		},
		
		/**
			* Initiate files upload for those which are not already uploading
		*/
		
		start: function() {
		
			if(ViewModels.files.list.length > 0){
			
				for(var i = 0; i < ViewModels.files.list.length; i++){
				
					var file = ViewModels.files.list[i];
					
					if(!file.inProgress){
					
						ViewModels.upload.launch(file);
						
						file.inProgress = true;
					
					}
				
				}
			
			}
		
		},
		
		/**
			* Launch upload
		*/
		
		launch: function(file) {
		
			file.server.parameters.create.callback = function(event) {
			
				file.server.listenUpload(event, file);
			
			};
			
			file.server.create({
				upload: 'Upload',
				file: file.data
			});
		
		},
		
		/**
			* We add to App.Server to listener, one for global upload and another for the progress rate
		*/
		
		extendAppServer: function() {
		
			App.Server.prototype.listenUpload = function(event, file) {
			
				//attach listener on the file upload progress
				event.target.upload.addEventListener(
					'progress', 
					function(e) {
						file.server.listenProgress(e, file)
					}, 
					false
				);
				
				var resp = file.server.listen(event);
				
				if(resp !== false){
				
					var menu = ViewModels.upload.menu,
						img = '',
						link = '';
					
					if(resp.message === true){
					
						file.id = resp.infos[0].id;
						file.type = resp.infos[0].type;
						file.path = resp.infos[0].path;
						file.thumb150 = resp.infos[0].thumb150;
						file.thumb300 = resp.infos[0].thumb300;
						file.thumb1000 = resp.infos[0].thumb1000;
						file.editUrl = resp.infos[0].edit_url;
						file.editWord = resp.infos[0].edit_word;
						file.uploaded = true;
						
						if(file.type.substring(0, 5) === 'image')
							img = '<a class="fancybox" href="'+file.path+'"><img src="'+file.thumb150+'" alt="" /></a>';
						else if(file.type.substring(0, 5) === 'video')
							img = '<img src="'+file.path+'" alt="video" />';
						
						link = '<a href="'+file.editUrl+'">'+file.editWord+'</a>';
						
						file.element.find('.fl_progress').hide();
						file.element.find('.fl_image').html(img);
						file.element.find('.fl_link').show().html(link);
					
					}else{
					
						file.uploaded = true;
						
						menu.after(((resp.message)?resp.message:'<div class="message wrong">A server error occured</div>'));
						file.element.remove();
						ViewModels.messages.recheck();
					
					}
				
				}
			
			};
			
			App.Server.prototype.listenProgress = function(event, file) {
			
				var percent = null;
				
				if(event.lengthComputable){
				
					percent = Math.round(event.loaded * 100 / event.total);
					
					file.element.find('.bar').css('width', percent+'%');
				
				}else{
				
					console.log('Can\'t compute upload for "'+file.name+'"');
				
				}
			
			};
		
		}
	
	};
	
	ViewModels.upload.init();

});