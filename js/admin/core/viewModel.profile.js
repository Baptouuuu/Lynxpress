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
	* Used with \Admin\Users\Controllers\Profile
	*
	* @package	ViewModels
*/

$(document).ready(function() {

	ViewModels.profile = {
	
		select: $('#pn'),
		fn: $('#fn'),
		ln: $('#ln'),
		nn: $('#nn'),
		
		init: function() {
		
			this.fn.on('keyup', this.updatePn);
			this.ln.on('keyup', this.updatePn);
			this.nn.on('keyup', this.updatePn);
		
		},
		
		updatePn: function() {
		
			var id = $(this).attr('id'),
				value = $(this).val(),
				opt_fn = ViewModels.profile.select.children('#opt_fn'),
				opt_ln = ViewModels.profile.select.children('#opt_ln'),
				opt_lnfn = ViewModels.profile.select.children('#opt_lnfn'),
				opt_fnln = ViewModels.profile.select.children('#opt_fnln');
				opt_nn = ViewModels.profile.select.children('#opt_nn');
			
			if(id === 'fn'){
			
				if(value === ''){
				
					opt_fn.remove();
					opt_lnfn.remove();
					opt_fnln.remove();
					
					return false;
				
				}
				
				if(opt_fn.length !== 0){
				
					opt_fn.text(value);
				
				}else{
				
					ViewModels.profile.select.append('<option id="opt_fn">'+value+'</option>');
				
				}
				
				if(opt_lnfn.length !== 0 && ViewModels.profile.ln.val() !== ''){
				
					opt_lnfn.text(ViewModels.profile.ln.val()+' '+value);
				
				}else if(ViewModels.profile.ln.val() !== ''){
				
					ViewModels.profile.select.append('<option id="opt_lnfn">'+ViewModels.profile.ln.val()+' '+value+'</option>');
				
				}
				
				if(opt_fnln.length !== 0 && ViewModels.profile.ln.val() !== ''){
				
					opt_fnln.text(value+' '+ViewModels.profile.ln.val());
				
				}else if(ViewModels.profile.ln.val() !== ''){
				
					ViewModels.profile.select.append('<option id="opt_fnln">'+value+' '+ViewModels.profile.ln.val()+'</option>');
				
				}
			
			}else if(id === 'ln'){
			
				if(value === ''){
				
					opt_ln.remove();
					opt_lnfn.remove();
					opt_fnln.remove();
					
					return false;
				
				}
				
				if(opt_ln.length !== 0){
				
					opt_ln.text(value);
				
				}else{
				
					ViewModels.profile.select.append('<option id="opt_ln">'+value+'</option>');
				
				}
				
				if(opt_lnfn.length !== 0 && ViewModels.profile.fn.val() !== ''){
				
					opt_lnfn.text(value+' '+ViewModels.profile.fn.val());
				
				}else if(ViewModels.profile.fn.val() !== ''){
				
					ViewModels.profile.select.append('<option id="opt_lnfn">'+value+' '+ViewModels.profile.fn.val()+'</option>');
				
				}
				
				if(opt_fnln.length !== 0 && ViewModels.profile.fn.val() !== ''){
				
					opt_fnln.text(ViewModels.profile.fn.val()+' '+value);
				
				}else if(ViewModels.profile.fn.val() !== ''){
				
					ViewModels.profile.select.append('<option id="opt_fnln">'+ViewModels.profile.fn.val()+' '+value+'</option>');
				
				}
			
			}else if(id === 'nn'){
			
				if(value === '')
					opt_nn.remove();
				
				if(opt_nn.length !== 0)
					opt_nn.text(value);
				else
					ViewModels.profile.select.append('<option id="opt_nn">'+value+'</option>');
			
			}
		
		}
	
	};
	
	ViewModels.profile.init();

});