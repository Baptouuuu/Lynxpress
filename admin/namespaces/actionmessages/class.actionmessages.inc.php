<?php

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
	
	namespace Admin\ActionMessages;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
	
	/**
		* ActionMessages
		*
		* Regroup methods to display action messages
		*
		* Messages are mainly returned in _action_msg attribute
		*
		* @package		Admin
		* @namespace	ActionMessages
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.2
		* @abstract
	*/
	
	abstract class ActionMessages{
	
		const MSG_G_B = '<div class="message good">';
		const MSG_W_B = '<div class="message wrong">';
		const MSG_E = '<span class=close>x</span></div>';
		
		/**
			* Generic method that returns wanted message on success action
			*
			* @static
			* @access	public
			* @param	string [$msg]
			* @return	string Message with a style for a good action
		*/
		
		public static function custom_good($msg){
		
			return self::MSG_G_B.$msg.self::MSG_E;
		
		}
		
		/**
			* Generic method that returns wanted message on aborted action
			*
			* @static
			* @access	public
			* @param	string [$msg]
			* @return	string Message with a style for a wrong action
		*/
		
		public static function custom_wrong($msg){
		
			return self::MSG_W_B.$msg.self::MSG_E;
		
		}
		
		/**
			* Method that return appropriated message on deletion event
			*
			* @static
			* @access	public
			* @param	mixed [$value]
			* @return	string Message if elements has been deleted or not, or the message from a raisen exception
		*/
		
		public static function deleted($value){
		
			if($value === true)
				return self::custom_good(Lang::_('Item(s) deleted', 'actionmessages'));
			elseif($value === false)
				return self::custom_wrong(Lang::_('Item(s) not deleted!', 'actionmessages'));
			else
				return self::custom_wrong($value);
		
		}
		
		/**
			* Method that return appropriated message on creation event
			*
			* @static
			* @access	public
			* @param	mixed [$value]
			* @return	string Message if elements has been created or not, or the message from a raisen exception
		*/
		
		public static function created($value){
		
			if($value === true)
				return self::custom_good(Lang::_('Item(s) added', 'actionmessages'));
			elseif($value === false)
				return self::custom_wrong(Lang::_('Item(s) not added!', 'actionmessages'));
			else
				return self::custom_wrong($value);
		
		}
		
		/**
			* Method that return appropriated message on update event
			*
			* @static
			* @access	public
			* @param	mixed [$value]
			* @return	string Message if elements has been updated or not, or the message from a raisen exception
		*/
		
		public static function updated($value){
		
			if($value === true)
				return self::custom_good(Lang::_('Item(s) updated', 'actionmessages'));
			elseif($value === false)
				return self::custom_wrong(Lang::_('Item(s) not updated!', 'actionmessages'));
			else
				return self::custom_wrong($value);
		
		}
		
		/**
			* Method that return appropriated message on multiple errors
			*
			* @static
			* @access	public
			* @param	array [$array]
			* @return	string List of errors
		*/
		
		public static function errors(array $array){
		
			$string = null;
			
			foreach($array as $e)
				$string .= '<li> - '.$e.'</li>';
			
			return self::custom_wrong(Lang::_('Check your informations', 'actionmessages').':<ul>'.$string.'</ul>');
		
		}
		
		/**
			* Method that returns message if user don't have permission to access to an area
			*
			* @static
			* @access	public
			* @return	string Message if the user doesn't have the right to access an administration page
		*/
		
		public static function part_no_perm(){
		
			return self::custom_wrong(Lang::_('You can\'t manage this part!', 'actionmessages'));
		
		}
		
		/**
			* Method that returns message if user don't have permission to do an action
			*
			* @static
			* @access	public
			* @return	string Message if the user doesn't have the right to do a specific action, for example for deleting content
		*/
		
		public static function action_no_perm(){
		
			return self::custom_wrong(Lang::_('You don\'t have permission to do this action!', 'actionmessages'));
		
		}
		
		/**
			* Method that return appropriated message on profile update
			*
			* @static
			* @access	public
			* @param	mixed [$value]
			* @return	string Message if elements has been updated or not, or the message from a raisen exception
		*/
		
		public static function profile_updated($value){
		
			if($value === true)
				return self::custom_good(Lang::_('Profile updated', 'actionmessages'));
			elseif($value === false)
				return self::custom_wrong(Lang::_('Profile not updated!', 'actionmessages'));
			else
				return self::custom_wrong($value);
		
		}
		
		/**
			* Method that return appropriated message when an item is trashed
			*
			* @static
			* @access	public
			* @param	mixed [$value]
			* @return	string Message if elements has been updated or not, or the message from a raisen exception
		*/
		
		public static function trashed($value){
		
			if($value === true)
				return self::custom_good(Lang::_('Item(s) trashed', 'actionmessages'));
			elseif($value === false)
				return self::custom_wrong(Lang::_('Item(s) not trashed!', 'actionmessages'));
			else
				return self::custom_wrong($value);
		
		}
		
		/**
			* Method that return appropriated message when an item is restored
			*
			* @static
			* @access	public
			* @param	mixed [$value]
			* @return	string Message if elements has been updated or not, or the message from a raisen exception
		*/
		
		public static function restored($value){
		
			if($value === true)
				return self::custom_good(Lang::_('Item(s) restored', 'actionmessages'));
			elseif($value === false)
				return self::custom_wrong(Lang::_('Item(s) not restored!', 'actionmessages'));
			else
				return self::custom_wrong($value);
		
		}
		
		/**
			* Method that return appropriated message if an update is available or not
			*
			* @static
			* @access	public
			* @param	boolean	[$value]
			* @param	boolean	[$link] Display a link to go to update page
			* @return	string Message if elements has been updated or not, or the message from a raisen exception
		*/
		
		public static function update_available($value, $link = false){
		
			if($value === true)
				return self::custom_good(Lang::_('System update available.'.(($link)?' <a href="'.Url::_(array('ns' => 'update')).'">'.Lang::_('Update').'</a>':''), 'actionmessages'));
			elseif($value === false)
				return self::custom_wrong(Lang::_('No update available!', 'actionmessages'));
		
		}
	
	}

?>