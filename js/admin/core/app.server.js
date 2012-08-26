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

var App = App || {};

/**
	* Send easily request to the server
	*
	* @package	App
*/

App.Server = function(object){

	this.parameters = object || {
		create: {
			url: '',
			method: 'post',
			callback: this.listen
		},
		read: {
			url: '',
			method: 'get',
			callback: this.listen
		},
		update: {
			url: '',
			method: 'post',
			callback: this.listen
		},
		delete: {
			url: '',
			method: 'post',
			callback: this.listen
		}
	};

};

App.Server.prototype = {

	create: function(object) {
	
		var xhr = new XMLHttpRequest,
			data = new FormData;
		
		if(object !== undefined)
			for(var property in object)
				data.append(property, object[property]);
		
		xhr.addEventListener(
			'readystatechange', 
			this.parameters.create.callback,
			false
		);
		
		xhr.open(this.parameters.create.method, this.parameters.create.url);
		xhr.send(data);
		
		return true;
	
	},
	
	read: function(object) {
	
		var xhr = new XMLHttpRequest,
			data = new FormData;
		
		if(object !== undefined)
			for(var property in object)
				data.append(property, object[property]);
		
		xhr.addEventListener(
			'readystatechange', 
			this.parameters.read.callback,
			false
		);
		
		xhr.open(this.parameters.read.method, this.parameters.read.url);
		xhr.send(data);
		
		return true;
	
	},
	
	update: function(object) {
	
		var xhr = new XMLHttpRequest,
			data = new FormData;
		
		if(object !== undefined)
			for(var property in object)
				data.append(property, object[property]);
		
		xhr.addEventListener(
			'readystatechange', 
			this.parameters.update.callback,
			false
		);
		
		xhr.open(this.parameters.update.method, this.parameters.update.url);
		xhr.send(data);
		
		return true;
	
	},
	
	delete: function(object) {
	
		var xhr = new XMLHttpRequest,
			data = new FormData;
		
		if(object !== undefined)
			for(var property in object)
				data.append(property, object[property]);
		
		xhr.addEventListener(
			'readystatechange', 
			this.parameters.delete.callback,
			false
		);
		
		xhr.open(this.parameters.delete.method, this.parameters.delete.url);
		xhr.send(data);
		
		return true;
	
	},
	
	listen: function(event) {
	
		if(event.target.readyState == 4 && (event.target.status == 200 || event.target.status == 0)){
		
			var resp = null;
			
			try{
			
				resp = JSON.parse(event.target.response);
			
			}catch(e){
			
				resp = e.message;
			
			}
			
			return resp;
		
		}
		
		return false;
	
	}

};