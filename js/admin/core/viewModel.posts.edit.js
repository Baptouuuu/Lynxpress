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
	* Used with \Admin\Posts\Controllers\Edit
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	/**
		* Store media objects retrieved from html datalist tags
	*/
	
	ViewModels.datalists = {
	
		pictures: [],
		videos: [],
		albums: [],
		
		init: function() {
		
			this.loadPictures();
			this.loadVideos();
			this.loadAlbums();
		
		},
		
		/**
			* Load pictures datalist as objects
		*/
		
		loadPictures: function() {
		
			var datalist = $('datalist#pictures'),
				options = datalist.children('option');
			
			$.each(options, function() {
			
				var $this = $(this),
					obj = {
						id: $this.data('id'),
						name: $this.data('name'),
						description: $this.data('description'),
						permalink: $this.data('permalink'),
						permalink_150: $this.data('permalink-150'),
						permalink_300: $this.data('permalink-300'),
						permalink_1000: $this.data('permalink-1000'),
						checked: ((datalist.data('banner') == $this.data('id'))?'checked':'')
					};
				
				ViewModels.datalists.pictures.push(obj);
			
			});
		
		},
		
		/**
			* Load videos datalist as objects
		*/
		
		loadVideos: function() {
		
			var datalist = $('datalist#videos'),
				options = datalist.children('option');
			
			$.each(options, function() {
			
				var $this = $(this),
					obj = {
						id: $this.data('id'),
						name: $this.data('name'),
						permalink: $this.data('permalink'),
						fallback: $this.data('fallback')
					};
				
				ViewModels.datalists.videos.push(obj);
			
			});
		
		},
		
		/**
			* Load albums datalist as objects
		*/
		
		loadAlbums: function() {
		
			var datalist = $('datalist#albums'),
				options = datalist.children('option');
			
			$.each(options, function() {
			
				var $this = $(this),
					obj = {
						id: $this.data('id'),
						name: $this.data('name'),
						permalink: $this.data('permalink'),
						checked: ((datalist.data('gallery') == $this.data('id'))?'checked':'')
					};
				
				ViewModels.datalists.albums.push(obj);
			
			});
		
		}
	
	};
	
	ViewModels.datalists.init();
	
	/**
		* Handle the display of popups
	*/
	
	ViewModels.post = {
	
		buttons: $('.button[data-form]'),
		popups: $('#popups'),
		background: $('#popups .background'),
		cancels: null,
		current: null,
		
		init: function() {
		
			this.fillPictures();
			this.fillVideos();
			this.fillBanners();
			this.fillGalleries();
			
			this.cancels = this.popups.find('.cancel');
			
			this.buttons.on('click', this.displayPopup);
			
			this.background.on('click', this.hidePopup);
			this.cancels.on('click', this.hidePopup);
		
		},
		
		/**
			* Fill pictures list with objects from ViewModels.datalists
		*/
		
		fillPictures: function() {
		
			var toInsert = '';
			
			$.each(ViewModels.datalists.pictures, function() {
			
				toInsert += Views.post.makePicture(
					this.id,
					this.name,
					this.description,
					this.permalink,
					this.permalink_150,
					this.permalink_300,
					this.permalink_1000
				);
			
			});
			
			this.popups.find('#add_image_form ul').append(toInsert);
		
		},
		
		/**
			* Fill videos list with objects from ViewModels.datalists
		*/
		
		fillVideos: function() {
		
			var toInsert = '';
			
			$.each(ViewModels.datalists.videos, function() {
			
				toInsert += Views.post.makeVideo(
					this.id,
					this.name,
					this.permalink,
					this.fallback
				);
			
			});
			
			this.popups.find('#add_video_form ul').append(toInsert);
		
		},
		
		/**
			* Fill banners list with objects from ViewModels.datalists
		*/
		
		fillBanners: function() {
		
			var toInsert = '';
			
			$.each(ViewModels.datalists.pictures, function() {
			
				toInsert += Views.post.makeBanner(
					this.id,
					this.name,
					this.description,
					this.permalink,
					this.permalink_150,
					this.permalink_300,
					this.permalink_1000,
					this.checked
				);
			
			});
			
			this.popups.find('#pbi_form ul').append(toInsert);
		
		},
		
		/**
			* Fill galleries list with objects from ViewModels.datalists
		*/
		
		fillGalleries: function() {
		
			var toInsert = '';
			
			$.each(ViewModels.datalists.albums, function() {
			
				toInsert += Views.post.makeGallery(
					this.id,
					this.name,
					this.permalink,
					this.checked
				);
			
			});
			
			this.popups.find('#pga_form ul').append(toInsert);
		
		},
		
		/**
			* Display a popup when a button is clicked
		*/
		
		displayPopup: function(event) {
		
			event.preventDefault();
			
			ViewModels.post.current = ViewModels.post.popups.find('#'+$(this).data('form'));
			
			ViewModels.post.popups.show(0, function() {
			
				ViewModels.post.current.show(0, function() {
				
					ViewModels.post.background.addClass('show');
					ViewModels.post.current.addClass('show');
				
				});
			
			});
		
		},
		
		/**
			* Hide a popup when the black background or a cancel button is clicked
		*/
		
		hidePopup: function(event) {
		
			event.preventDefault();
			
			ViewModels.post.resetScreen();
		
		},
		
		/**
			* Hide a popup from the screen
			* Can be called from an other object
		*/
		
		resetScreen: function() {
		
			ViewModels.post.current.removeClass('show');
			ViewModels.post.background.removeClass('show');
			
			ViewModels.post.current.hide();
			ViewModels.post.popups.hide();
			
			ViewModels.post.current = null;
		
		}
	
	};
	
	ViewModels.post.init();
	
	/**
		* Makes a backup of non submitted data from the user
	*/
	
	ViewModels.storage = {
	
		id: $('#post_id').val(),
		title: $('#pf_title'),
		content: $('#pf_content'),
		object: null,
		resetButton: $('.button.clear'),
		form: $(document.forms[0]),
		
		init: function() {
		
			this.load();
			
			this.title.on('change', this.save);
			this.content.on('change', this.save);
			
			this.resetButton.on('click', this.delete);
			this.form.on('submit', this.delete);
		
		},
		
		/**
			* Load data from localStorage, and if the data is different we replace it in the DOM
		*/
		
		load: function() {
		
			this.object = new Models.Post(this.id);
			
			if(this.object.exist()){
			
				if(this.object.title !== '' && this.object.title !== this.title.val()){
				
					this.title.val(this.object.title);
					this.title.addClass('from_localstorage');
					
					this.resetButton.show();
				
				}
				
				if(this.object.content !== '' && this.object.content !== this.content.val()){
				
					this.content.val(this.object.content);
					this.content.addClass('from_localstorage');
					
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
		
			ViewModels.storage.title.removeClass('from_localstorage');
			ViewModels.storage.content.removeClass('from_localstorage');
			
			ViewModels.storage.object.delete();
			
			ViewModels.storage.resetButton.hide();
		
		}
	
	};
	
	ViewModels.storage.init();
	
	/**
		* Add an anchor tag to the post textarea
	*/
	
	ViewModels.addLink = {
	
		name: ViewModels.post.popups.find('#ad_name'),
		url: ViewModels.post.popups.find('#ad_url'),
		addButton: ViewModels.post.popups.find('#add_link_form .button.add'),
		
		init: function() {
		
			this.addButton.on('click', this.add);
		
		},
		
		/**
			* Makes verification on the data inserted before adding the link
		*/
		
		add: function(event) {
		
			var html = null;
			
			event.preventDefault();
			
			if(ViewModels.addLink.name.val() === '' && (ViewModels.addLink.url.val() === '' || ViewModels.addLink.url.is(':invalid'))){
			
				ViewModels.addLink.name.addClass('wrong');
				ViewModels.addLink.url.addClass('wrong');
				
				return false;
			
			}
			
			if(ViewModels.addLink.name.val() === ''){
			
				ViewModels.addLink.name.addClass('wrong');
				
				return false;
			
			}else{
			
				ViewModels.addLink.name.removeClass('wrong');
			
			}
			
			if(ViewModels.addLink.url.val() === '' || ViewModels.addLink.url.is(':invalid')){
			
				ViewModels.addLink.url.addClass('wrong');
				
				return false;
			
			}else{
			
				ViewModels.addLink.url.removeClass('wrong');
			
			}
			
			html = '<a href="'+ViewModels.addLink.url.val()+'">'+ViewModels.addLink.name.val()+'</a>';
			
			ViewModels.storage.content.text(ViewModels.storage.content.text()+html);
			
			ViewModels.addLink.hide();
		
		},
		
		/**
			* If the anchor tag is added we clean the screen from the popup and reset form inputs
		*/
		
		hide: function() {
		
			this.name.val(null);
			this.url.val(null);
			
			this.name.removeClass('wrong');
			this.url.removeClass('wrong');
			
			ViewModels.post.resetScreen();
		
		}
	
	};
	
	ViewModels.addLink.init();
	
	/**
		* Add an h3 tag to the post textarea
	*/
	
	ViewModels.addTitle = {
	
		title: ViewModels.post.popups.find('#at_title'),
		addButton: ViewModels.post.popups.find('#add_title_form .button.add'),
		
		init: function() {
		
			this.addButton.on('click', this.add);
		
		},
		
		/**
			* Check if the input is not empty for not adding a blank h3
		*/
		
		add: function(event) {
		
			var html = null;
			
			event.preventDefault();
			
			if(ViewModels.addTitle.title.val() === ''){
			
				ViewModels.addTitle.title.addClass('wrong');
				
				return false;
			
			}else{
			
				ViewModels.addTitle.title.removeClass('wrong');
			
			}
			
			html = '<h3>'+ViewModels.addTitle.title.val()+'</h3>';
			
			ViewModels.storage.content.text(ViewModels.storage.content.text()+html);
			
			ViewModels.addTitle.hide();
		
		},
		
		/**
			* If the h3 tag is added we clean the screen from the popup and reset form input
		*/
		
		hide: function() {
		
			this.title.val(null);
			
			this.title.removeClass('wrong');
			
			ViewModels.post.resetScreen();
		
		}
	
	};
	
	ViewModels.addTitle.init();
	
	/**
		* Allow the user to add images to the post textarea
	*/
	
	ViewModels.addImage = {
	
		addButtons: ViewModels.post.popups.find('#add_image_form .add_button'),
		
		init: function() {
		
			this.addButtons.on('click', this.add);
		},
		
		/**
			* Add an image to the post textarea
		*/
		
		add: function(event) {
		
			var $this = $(this),
				html = null;
			
			event.preventDefault();
			
			html = '<a href="'+$this.data('full')+'" data-rel=lightbox rel=post><img src="'+$this.data('link')+'" alt="'+$this.data('name')+' | '+$this.data('description')+'" /></a>';
			
			ViewModels.storage.content.text(ViewModels.storage.content.text()+html);
			ViewModels.storage.content.change();
		
		}
	
	};
	
	ViewModels.addImage.init();
	
	/**
		* Allow the user to add videos to the post textarea
	*/
	
	ViewModels.addVideo = {
	
		addButtons: ViewModels.post.popups.find('#add_video_form .add_button'),
		
		init: function() {
		
			this.addButtons.on('click', this.add);
		},
		
		/**
			* Add a video to the post textarea
		*/
		
		add: function(event) {
		
			var $this = $(this),
				html = null;
			
			event.preventDefault();
			
			html = '<video src="'+$this.data('link')+'" controls preload="metadata">'+$this.data('fallback')+'</video>';
			
			ViewModels.storage.content.text(ViewModels.storage.content.text()+html);
			ViewModels.storage.content.change();
		
		}
	
	};
	
	ViewModels.addVideo.init();

});