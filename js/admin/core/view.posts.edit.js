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

var Views = Views || {};

/**
	* Used with \Admin\Posts\Controllers\Edit
	*
	* @package	Views
*/

$(document).ready(function() {

	Views.post = {
	
		template: {
		
			picture: '',
			video: '',
			banner: '',
			gallery: ''
		
		},
		
		init: function() {
		
			this.template.picture = $('#tpl_picture').html();
			this.template.video = $('#tpl_video').html();
			this.template.banner = $('#tpl_banner').html();
			this.template.gallery = $('#tpl_gallery').html();
		
		},
		
		makePicture: function(id, name, description, permalink, permalink_150, permalink_300, permalink_1000) {
		
			return this.template.picture.replace(/{{id}}/g, id)
										.replace(/{{name}}/g, name)
										.replace(/{{description}}/g, description)
										.replace(/{{permalink}}/g, permalink)
										.replace(/{{permalink\-150}}/g, permalink_150)
										.replace(/{{permalink\-300}}/g, permalink_300)
										.replace(/{{permalink\-1000}}/g, permalink_1000);
		
		},
		
		makeVideo: function(id, name, permalink, fallback) {
		
			return this.template.video.replace(/{{id}}/g, id)
									  .replace(/{{name}}/g, name)
									  .replace(/{{permalink}}/g, permalink)
									  .replace(/{{fallback}}/g, fallback);
		
		},
		
		makeBanner: function(id, name, description, permalink, permalink_150, permalink_300, permalink_1000, checked) {
		
			return this.template.banner.replace(/{{id}}/g, id)
										.replace(/{{name}}/g, name)
										.replace(/{{description}}/g, description)
										.replace(/{{permalink}}/g, permalink)
										.replace(/{{permalink\-150}}/g, permalink_150)
										.replace(/{{permalink\-300}}/g, permalink_300)
										.replace(/{{permalink\-1000}}/g, permalink_1000)
										.replace(/{{checked}}/g, checked);
		
		},
		
		makeGallery: function(id, name, permalink, checked) {
		
			return this.template.gallery.replace(/{{id}}/g, id)
										.replace(/{{name}}/g, name)
										.replace(/{{permalink}}/g, permalink)
										.replace(/{{checked}}/g, checked);
		
		}
	
	};
	
	Views.post.init();

});