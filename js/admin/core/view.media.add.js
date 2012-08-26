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
	* Used with \Admin\Media\Controllers\Add
	*
	* @package	Views
*/

$(document).ready(function() {

	Views.files = {
	
		template: '',
		
		init: function() {
		
			this.template = $('#tpl_media').html();
		
		},
		
		makeMedia: function(id, name, size) {
		
			return this.template.replace(/{{id}}/g, id)
								.replace(/{{name}}/g, name)
								.replace(/{{size}}/g, this.calcSize(size));
		
		},
		
		calcSize: function(bits) {
		
			if(bits > 1024 * 1024)
			  return (Math.round(bits * 100 / (1024 * 1024)) / 100).toString() + 'MB';
			else
			  return (Math.round(bits * 100 / 1024) / 100).toString() + 'KB';
		
		}
	
	};
	
	Views.files.init();

});