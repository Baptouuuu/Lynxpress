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
	* Handles videos elements on the page
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	ViewModels.videos = {
	
		elements: $('video'),
		objects: [],
		
		init: function() {
		
			for(var i = 0; i < this.elements.length; i++)
				this.create(this.elements[i]);
		
		},
		
		/**
			* Initiate an object wrapper to control the video
		*/
		
		create: function(element) {
		
			var self = ViewModels.videos;
			
			self.objects.push(new ViewModels.Video(element));
		
		}
	
	};
	
	ViewModels.videos.init();

});