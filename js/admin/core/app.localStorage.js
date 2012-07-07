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
	* Easily manipulate data in the localStorage of a client browser
	*
	* @package	App
*/

App.localStorage = function(){

	if(localStorage !== undefined && JSON !== undefined){
	
		this.localStorage = localStorage;
	
	}else{
	
		this.localStorage = false;
		this.error = 'Your browser don\'t support localStorage or JSON object';
		console.log(this.error);
	
	}
	
	this.error = null;

};

App.localStorage.prototype = {

	/**
		* Insert data with a specific id, the value stored can be a string or an object
	*/
	
	create: function(id, object) {
	
		if(!this.localStorage)
			return false;
		
		var toStore = null;
		
		if(object instanceof Object)
			toStore = JSON.stringify(object);
		else
			toStore = object;
		
		this.localStorage.setItem(id, toStore);
		
		return true;
	
	},
	
	/**
		* Will return value for the specified id, if the data stored is JSON it will return data as an object
		* In case the data stored is JSON, you can access directly a property of the object via the 'property' parameter
	*/
	
	read: function(id, property) {
	
		if(!this.localStorage)
			return false;
		
		var retrieved = null;
		
		if(property !== undefined){
		
			try{
			
				retrieved = this.localStorage.getItem(id);
				
				retrieved = JSON.parse(retrieved)[property];
			
			}catch(e){
			
				this.error = e.message;
				retrieved = false;
			
			}
		
		}else{
		
			try{
			
				retrieved = JSON.parse(this.localStorage.getItem(id));
			
			}catch(e){
			
				retrieved = this.localStorage.getItem(id);
			
			}
		
		}
		
		return retrieved;
	
	},
	
	/**
		* Allow to update a stored element
		* You can update data as string or an object (this one will be inserted as JSON)
		* In case original data is JSON, you can update directly a property of this object
	*/
	
	update: function(id, value, property) {
	
		if(!this.localStorage)
			return false;
		
		var fromStore = null;
		
		if(property !== undefined){
		
			try{
			
				fromStore = JSON.parse(this.localStorage.getItem(id));
				fromStore[property] = value;
				this.localStorage.setItem(id, JSON.stringify(fromStore));
				
				return true;
			
			}catch(e){
			
				this.error = e.message;
				
				return false;
			
			}
		
		}else{
		
			var toStore = null;
			
			if(value instanceof Object)
				toStore = JSON.stringify(value);
			else
				toStore = value;
			
			this.localStorage.setItem(id, toStore);
			
			return true;
		
		}
	
	},
	
	/**
		* Allow to delete an element in the storage
	*/
	
	delete: function(id) {
	
		if(!this.localStorage)
			return false;
		
		this.localStorage.removeItem(id);
		
		return true;
	
	}

};