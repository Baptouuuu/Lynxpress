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

var Models = Models || {};

/**
	* Manipulate comment item data
	*
	* @package	Models
*/

Models.Comment = function(id){

	this.id = id || 0;
	this.name = '';
	this.email = '';
	this.content = '';
	this.rel_id = 0;
	this.rel_type = '';
	this.status = '';
	this.date = new Date();
	
	this.context = new App.localStorage;
	this.prefix = 'lynxpress_comment_';
	
	if(id !== undefined)
		this.read();

};

Models.Comment.prototype = {

	/**
		* Change the context where you want to manipulate item data (localStorage, Server)
		* If context is changed to Server, you'll have to specify parameters of this object to enable valid requests
	*/
	
	changeContext: function(context, parameters) {
	
		if(context !== undefined && parameters === undefined)
			this.context = new App[context];
		else if(context !== undefined && parameters !== undefined)
			this.context = new App[context](parameters);
		else
			return false;
		
		return true;
	
	},
	
	/**
		* Return an object with only comment data, allow to avoid passing the whole object with unwanted property 'context' and 'prefix'
	*/
	
	formatData: function() {
	
		return {
			id: this.id,
			email: this.email,
			content: this.content,
			rel_id: this.rel_id,
			rel_type: this.rel_type,
			status: this.status,
			date: this.date.getTime()
		};
	
	},
	
	/**
		* Create the comment in the wished context
	*/
	
	create: function() {
	
		if(App.localStorage && this.context instanceof App.localStorage)
			return this.context.create(
				this.prefix+this.id,
				this.formatData()
			);
		
		return false;
	
	},
	
	/**
		* Put data in the object from the wished context
		* If 'property' parameter is specified it will only retrieved wished one
	*/
	
	read: function(property) {
	
		if(property !== undefined){
		
			if(this.context instanceof App.localStorage){
			
				this[property] = this.context.read(this.prefix+this.id, property);
				
				if(property == 'date')
					this.date = new Date(this.date);
				
				return true;
			
			}
		
		}else{
		
			if(this.context instanceof App.localStorage){
			
				var fromDB = this.context.read(this.prefix+this.id);
				
				for(var p in fromDB)
					if(this[p] !== undefined)
						this[p] = fromDB[p];
				
				this.date = new Date(this.date);
			
				return true;
			
			}
		
		}
		
		return false;
	
	},
	
	/**
		* Update the data in the wished context
		* If no parameter passed, it updates the whole object
	*/
	
	update: function(property) {
	
		if(property !== undefined){
		
			if(this.context instanceof App.localStorage)
				return this.context.update(this.prefix+this.id, this[property], property);
		
		}else{
		
			if(this.context instanceof App.localStorage)
				return this.context.update(this.prefix+this.id, this.formatData());
		
		}
		
		return false;
	
	},
	
	/**
		* Delete object in the wished context
	*/
	
	delete: function() {
	
		if(this.context instanceof App.localStorage)
			return this.context.delete(this.prefix+this.id);
		
		return false;
	
	},
	
	exist : function() {
	
		if(App.localStorage && this.context instanceof App.localStorage)
			return (this.context.read(this.prefix+this.id) === null) ? false : true;
	
	}

};