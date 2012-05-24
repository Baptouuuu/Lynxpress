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

//Used with \Admin\Posts\Controllers\Edit

$(document).ready(function() {

	txta = {
	
		buttons_div: $('.txta_actions'),
		buttons: $('.txta_actions > .button'),
		popup_section: $('#txta_action_form'),
		popup_bg: $('#txta_action_form > .background'),
		cancel: $('#txta_action_form .popup > .header > .cancel'),
		popup: null,
		textarea: null,
		
		init: function() {
		
			this.textarea = $('#'+this.buttons_div.data('id'));
			
			this.buttons.on('click', this.display);
			
			this.popup_bg.on('click', this.hide);
			this.cancel.on('click', this.hide);
		
		},
		
		display: function(event) {
		
			event.preventDefault();
			
			txta.popup = $('#'+$(this).data('form'));
			
			txta.popup_section.show(0, function() {
			
				txta.popup.show(0, function() {
				
					txta.popup_bg.addClass('show');
					txta.popup.addClass('show');
				
				});
			
			});
		
		},
		
		hide: function(event) {
		
			event.preventDefault();
		
			txta.popup.removeClass('show');
			txta.popup_bg.removeClass('show');
			
			txta.popup.hide();
			txta.popup_section.hide();
			
			txta.popup = null;
		
		},
		
		reset: function() {
		
			this.popup.removeClass('show');
			this.popup_bg.removeClass('show');
			
			this.popup.hide();
			this.popup_section.hide();
			
			this.popup = null;
		
		}
	
	};
	
	add_link = {
	
		name: $('#ad_name'),
		url: $('#ad_url'),
		add_button: $('#add_link_form .button.add'),
		
		init: function() {
		
			this.add_button.on('click', this.add);
		
		},
		
		add: function(event) {
		
			var html = null;
			
			event.preventDefault();
			
			if(add_link.name.val() === '' && (add_link.url.val() === '' || add_link.url.is(':invalid'))){
			
				add_link.name.addClass('wrong');
				add_link.url.addClass('wrong');
				return false;
			
			}
			
			if(add_link.name.val() === ''){
			
				add_link.name.addClass('wrong');
				return false;
			
			}else{
			
				add_link.name.removeClass('wrong');
			
			}
			
			if(add_link.url.val() === '' || add_link.url.is(':invalid')){
			
				add_link.url.addClass('wrong');
				return false;
			
			}else{
			
				add_link.url.removeClass('wrong');
			
			}
			
			html = '<a href="'+add_link.url.val()+'">'+add_link.name.val()+'</a>';
			
			txta.textarea.text(txta.textarea.text()+html);
			
			add_link.hide();
		
		},
		
		hide: function() {
		
			this.name.val(null);
			this.url.val(null);
			
			this.name.removeClass('wrong');
			this.url.removeClass('wrong');
			
			txta.reset();
		
		}
	
	};
	
	add_title = {
	
		title: $('#at_title'),
		add_button: $('#add_title_form .button.add'),
		
		init: function() {
		
			this.add_button.on('click', this.add);
		
		},
		
		add: function(event) {
		
			var html = null;
			
			event.preventDefault();
			
			if(add_title.title.val() === ''){
			
				add_title.title.addClass('wrong');
				return false;
			
			}else{
			
				add_title.title.removeClass('wrong');
			
			}
			
			html = '<h3>'+add_title.title.val()+'</h3>';
			
			txta.textarea.text(txta.textarea.text()+html);
			
			add_title.hide();
		
		},
		
		hide: function() {
		
			this.title.val(null);
			
			this.title.removeClass('wrong');
			
			txta.reset();
		
		}
	
	};
	
	add_image = {
	
		add_buttons: $('#add_image_form .add_button'),
		
		init: function() {
		
			this.add_buttons.on('click', this.add);
		},
		
		add: function(event) {
		
			var $this = $(this),
				html = null;
			
			event.preventDefault();
			
			html = '<a href="'+$this.data('full')+'" rel="fancybox"><img src="'+$this.data('link')+'" alt="'+$this.data('name')+' | '+$this.data('description')+'" /></a>';
			
			txta.textarea.text(txta.textarea.text()+html);
			txta.textarea.change();
		
		}
	
	};
	
	add_video = {
	
		add_buttons: $('#add_video_form .add_button'),
		
		init: function() {
		
			this.add_buttons.on('click', this.add);
		},
		
		add: function(event) {
		
			var $this = $(this),
				html = null;
			
			event.preventDefault();
			
			html = '<video src="'+$this.data('link')+'" controls preload="metadata">'+$this.data('fallback')+'</video>';
			
			txta.textarea.text(txta.textarea.text()+html);
			txta.textarea.change();
		
		}
	
	};
	
	extra = {
	
		button: $('#post_extra_buttons > .modify'),
		popup_section: $('#post_extra_form'),
		popup_bg: $('#post_extra_form > .background'),
		popup: null,
		cancel: $('#post_extra_form .popup > .header > .cancel'),
		
		init: function() {
		
			this.button.on('click', this.show);
			this.popup_bg.on('click', this.hide);
			this.cancel.on('click', this.hide);
		
		},
		
		show: function(event) {
		
			var $this = $(this);
			
			event.preventDefault();
			
			extra.popup = $('#'+$this.data('form'));
			
			extra.popup_section.show(0, function() {
			
				extra.popup.show(0, function() {
				
					extra.popup_bg.addClass('show');
					extra.popup.addClass('show');
				
				});
			
			});
		
		},
		
		hide: function(event) {
		
			event.preventDefault();
			
			extra.popup_bg.removeClass('show');
			extra.popup.removeClass('show');
			
			extra.popup.hide();
			extra.popup_section.hide();
			
			extra.popup = null;
		
		}
	
	};
	
	txta.init();
	add_link.init();
	add_title.init();
	add_image.init();
	add_video.init();
	extra.init();

});