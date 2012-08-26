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
	
	namespace Site\Install\Html;
	use \Library\Lang\Lang;
	
	defined('FOOTPRINT') or die();
	
	/**
		* Views for home controller
		*
		* @package		Site
		* @subpackage	Install\Html
		* @author		Baptiste Langlade <lynxpressorg@gmail.com>
		* @version		1.0
		* @abstract
	*/
	
	abstract class Home{
	
		/**
			* Display a welcome message to the user
			*
			* @static
			* @access	public
		*/
		
		public static function welcome(){
		
			echo '<div id=welcome>'.
					'<h1>'.Lang::_('Welcome to the Lynxpress installation', 'install').'</h1>'.
					'<p>'.
						Lang::_('First, thanks for downloading and using this project!', 'install').
					'</p>'.
					'<p>'.
						Lang::_('Before starting this setup, make sure you have the following informations:', 'install').
					'</p>'.
					'<ul>'.
						'<li>'.Lang::_('Database host', 'install').'</li>'.
						'<li>'.Lang::_('Database name', 'install').'</li>'.
						'<li>'.Lang::_('Database username', 'install').'</li>'.
						'<li>'.Lang::_('Database password', 'install').'</li>'.
						'<li>'.Lang::_('Table prefix', 'install').'</li>'.
						'<li>'.Lang::_('Website name', 'install').'</li>'.
						'<li>'.Lang::_('Website url', 'install').'</li>'.
						'<li>'.Lang::_('Website owner e-mail address', 'install').'</li>'.
					'</ul>'.
				 '</div>';
		
		}
		
		/**
			* Display database creation inputs
			*
			* @static
			* @access	public
		*/
		
		public static function database(){
		
			echo '<section id=database>'.
					'<h2>'.Lang::_('Database creation', 'install').'</h2>'.
					'<input class=input type=text name="db_host" value="localhost" placeholder="'.Lang::_('SQL server hosting the database', 'install').'" required /> <span class=tooltip>'.Lang::_('This information is given by your hoster', 'install').'</span><br/>'.
					'<input class=input type=text name="db_name" value="lynxpress" placeholder="'.Lang::_('Database name', 'install').'" required /> <span class=tooltip>'.Lang::_('If the database doesn\'t exist I\'ll try to create it', 'install').'</span><br/>'.
					'<input class=input type=text name="db_user" placeholder="'.Lang::_('Username', 'install').'" required /> <span class=tooltip>'.Lang::_('This information is given by your hoster', 'install').'</span><br/>'.
					'<input class=input type=text name="db_pwd" placeholder="'.Lang::_('Password', 'install').'" required /> <span class=tooltip>'.Lang::_('This information is given by your hoster', 'install').'</span><br/>'.
					'<input class=input type=text name="db_prefix" value="lp_" placeholder="'.Lang::_('Database tables prefix', 'install').'" /> <span class=tooltip>'.Lang::_('Useful if you want to install multiple website on the same database', 'install').'</span>'.
				 '</section>';
		
		}
		
		/**
			* Website related informations inputs
			*
			* @static
			* @access	public
		*/
		
		public static function website(){
		
			$dir = dirname($_SERVER['REQUEST_URI']);
			
			echo '<section id=website>'.
					'<h2>'.Lang::_('Website informations', 'install').'</h2>'.
					'<input class=input type=text name="ws_name" placeholder="'.Lang::_('Website name', 'install').'" required /><br/>'.
					'<input class=input type=url name="ws_url" value="http://'.$_SERVER['HTTP_HOST'].$dir.((!empty($dir) && $dir != '/')?'/':'').'" placeholder="'.Lang::_('Website url', 'install').'" required /> <span class=tooltip>'.Lang::_('IMPORTANT: the url must end with a "/"', 'install').'</span><br/>'.
					'<input class=input type=email name="ws_email" placeholder="example@lynxpress.org" required /> <span class=tooltip>'.Lang::_('Website owner e-mail address', 'install').'</span>'.
				 '</section>';
		
		}
		
		/**
			* Display user credentials inputs
			*
			* @static
			* @access	public
		*/
		
		public static function user(){
		
			echo '<section id=user>'.
					'<h2>'.Lang::_('Login credentials', 'install').'</h2>'.
					'<input class=input type=text name=username placeholder="'.Lang::_('Username', 'install').'" required /><br/>'.
					'<input class=input type=password name=password placeholder="'.Lang::_('Password', 'install').'" required />'.
				 '</section>';
		
		}
		
		/**
			* Display button to run the setup
			*
			* @static
			* @access	public
		*/
		
		public static function install(){
		
			echo '<div id=install>'.
					'<input class=button type=submit name=run value="'.Lang::_('Run setup', 'install').'" />'.
				 '</div>';
		
		}
		
		/**
			* Display form tag
			*
			* @static
			* @access	public
			* @param	string	[$part]
		*/
		
		public static function form($part){
		
			if($part == 'o')
				echo '<form action="#" method=post accept-charset="utf-8">';
			elseif($part == 'c')
				echo '</form>';
		
		}
		
		/**
			* Display an error message saying that inputs are missing
			*
			* @static
			* @access	public
		*/
		
		public static function missing_infos(){
		
			echo '<div class="message wrong">'.Lang::_('Some inputs are missing, make sure you filled them all.', 'install').'</div>';
		
		}
		
		/**
			* Display an error message saying lynxpress can't connect to mysql server
			*
			* @static
			* @access	public
		*/
		
		public static function unknown_host(){
		
			echo '<div class="message wrong">'.Lang::_('I can\'t connect to the mysql server! Please check your informations.', 'install').'</div>';
		
		}
		
		/**
			* Display an error message saying lynxpress can't create the database
			*
			* @static
			* @access	public
		*/
		
		public static function error_create_database(){
		
			echo '<div class="message wrong">'.Lang::_('I can\'t create the database! I\'m afraid you have to do it yourself.', 'install').'</div>';
		
		}
		
		/**
			* Display an error message saying lynxpress can't create tables
			*
			* @static
			* @access	public
		*/
		
		public static function error_create_table(){
		
			echo '<div class="message wrong">'.Lang::_('I can\'t create database tables!', 'install').'</div>';
		
		}
		
		/**
			* Display an error message saying lynxpress can't find config sample file
			*
			* @static
			* @access	public
		*/
		
		public static function config_missing(){
		
			echo '<div class="message wrong">'.Lang::_('I can\'t find configuration sample file!', 'install').'</div>';
		
		}
		
		/**
			* Display an error message saying lynxpress can't create config file
			*
			* @static
			* @access	public
			* @param	string	[$content] Config file content
		*/
		
		public static function error_config($content){
		
			echo '<div class="message wrong">'.
					Lang::_('I can\'t create the configuration file! Create the file "config.php", alongside the sample one, with the following content and then run this', 'install').' <a href="install.php?without_config=true">'.Lang::_('setup', 'install').'</a><br/>'.
					'<textarea class=txta autofocus>'.$content.'</textarea>'.
				 '</div>';
		
		}
		
		/**
			* Display an error message saying lynxpress can't fill database
			*
			* @static
			* @access	public
		*/
		
		public static function error_fill(){
		
			echo '<div class="message wrong">'.
					Lang::_('I can\'t fill the database with necessary informations!', 'install').
				 '</div>';
		
		}
		
		/**
			* Message displayed when the install is successful
			*
			* @static
			* @access	public
		*/
		
		public static function success(){
		
			echo '<section id=success>'.
					'<h1>'.Lang::_('Congratulations', 'install').'</h1>'.
					'<p>'.Lang::_(
						'Your website is now up and running! You can %login and start blogging.', 
						'install', 
						array(
							'login' => '<a class=button href="admin/">'.Lang::_('login', 'install').'</a>'
						)
					).
					'</p>'.
				 '</section>';
		
		}
	
	}

?>