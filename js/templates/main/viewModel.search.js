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
	* Download the list of published posts and save them in localStorage
	* Then it builds a datalist linked to the search bar in order to propose suggestions
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	ViewModels.search = {
	
		server: new App.Server,
		storage: new App.localStorage,
		data: [],
		date: new Date,
		
		init: function() {
		
			if(this.storage !== false){
			
				this.loadData();
			
			}
		
		},
		
		loadData: function() {
		
			var lastRetrieval = this.storage.read('lastRetrieval'),
				year = this.date.getFullYear().toString(),
				month = this.date.getMonth().toString(),
				day = this.date.getDate().toString();
			
			this.date = year+'-'+((month.length == 1)?'0':'')+month+'-'+((day.length == 1)?'0':'')+day;
			
			if(lastRetrieval === null || lastRetrieval === false || lastRetrieval < this.date){
			
				this.retrieveData();
			
			}else{
			
				this.data = this.storage.read('searchElements');
				this.buildDatalist();
			
			}
		
		},
		
		retrieveData: function() {
		
			this.server.parameters.read = {
				url: $('#search_datalist_url').data('url'),
				method: 'get',
				callback: this.retrieved
			};
			
			this.server.read();
		
		},
		
		retrieved: function(event) {
		
			var self = ViewModels.search,
				resp = self.server.listen(event);
			
			if(resp !== false){
				
				for(var i = 0; i < resp.length; i++)
					self.data.push(resp[i]._title);
				
				self.saveData();
				
				self.buildDatalist();
			
			}
		
		},
		
		saveData: function() {
		
			this.storage.create('searchElements', this.data);
			this.storage.create('lastRetrieval', this.date);
		
		},
		
		buildDatalist: function() {
		
			var datalist = '<datalist id=searchElements>';
			
			for(var i = 0; i < this.data.length; i++)
				datalist += '<option value="'+this.data[i]+'" />';
			
			datalist += '</datalist>';
			
			$(document.body).append(datalist);
		
		}
	
	};
	
	ViewModels.search.init();

});