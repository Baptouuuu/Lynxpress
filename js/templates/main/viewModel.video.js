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
	* Wrapper of video element to control it
	*
	* @package	ViewModels
*/

ViewModels.Video = function(element){

	var self = this,
		$el = $(element);
	
	this.element = element;
	this.id = this.element.dataset.id;
	this.name = this.element.dataset.name;
	this.mime = this.element.dataset.mime;
	this.user = this.element.dataset.user;
	this.controller = null;
	this.progressbar = null;
	this.progressdownload = null;
	this.supportVideo = true;
	
	this.init();
	
	if(this.supportVideo === true){
	
		this.progressbar = this.controller.find('.bar');
		this.progressdownload = this.controller.find('.download');
		
		this.controller.on('click', function(event){self.handle.call(self, event)});
		$el.on('timeupdate', function(event){self.update.call(self, event)});
		$el.on('play', function() {self.reset('reverse')});
		$el.on('progress', function(event) {self.progress.call(self, event)});
	
	}

};

ViewModels.Video.prototype = {

	init: function() {
	
		if(this.element.canPlayType(this.mime)){
		
			var $el = $(this.element),
				ctl = '<div id=ctl'+this.id+' class="controller init"><div class=name>'+this.name+'</div><div class=play></div><div class=progress><div class=download></div><div class=bar></div></div></div>';
			
			$el.after(ctl);
			
			this.element.removeAttribute('controls');
			this.controller = $('#ctl'+this.id);
			
			if(this.element.webkitEnterFullScreen || this.element.mozRequestFullScreen)
				this.controller.prepend('<div class=fullscreen></div>');
			
			this.supportVideo = true;
		
		}else{
		
			var fallback = this.element.innerHTML;
			
			this.element.parentNode.innerHTML = fallback;
			this.supportVideo = false;
		
		}
	
	},
	
	handle: function(event) {
	
		var t = event.target;
		
		if(t.classList.contains('play')){
			
			if(this.controller.hasClass('init')){
			
				this.element.play();
				this.reset('reverse');
				this.element.classList.add('playing');
			
			}else{
			
				this.element.pause();
				this.reset();
				this.element.classList.remove('playing');
			
			}
		
		}else if(t.classList.contains('fullscreen')){
		
			if(this.element.webkitEnterFullScreen)
				this.element.webkitEnterFullScreen();
			else if(this.element.mozRequestFullScreen)
				this.element.mozRequestFullScreen();
		
		}
	
	},
	
	update: function(event) {
	
		var percent = (this.element.currentTime / this.element.duration) * 100;
		
		this.progressbar.css('width', percent+'%');
		
		if(percent == 100)
			this.reset();
	
	},
	
	progress: function(event) {
	
		if(this.element.buffered.length > 0){
		
			var percent = (this.element.buffered.end(0) / this.element.duration) * 100;
			
			this.progressdownload.css('width', percent+'%');
		
		}
	
	},
	
	reset: function(dir) {
	
		if(dir !== 'reverse'){
		
			this.controller.removeClass('playing');
			this.controller.addClass('init');
		
		}else{
		
			this.controller.removeClass('init');
			this.controller.addClass('playing');
		
		}
	
	}

};