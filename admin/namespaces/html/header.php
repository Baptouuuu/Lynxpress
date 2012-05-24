<?php 
	use \Admin\Master\Helpers\Html as Helper;
	use \Library\Lang\Lang;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<title><?php echo $page->_title.' | '.WS_NAME ?></title>
		<link rel="index" href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>" />
		<link rel="icon" type="image/png" href="<?php echo WS_URL ?>images/lynxpress-mini.png" />
		<link rel="stylesheet" type="text/css" href="<?php echo WS_URL ?>css/admin/main.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo WS_URL ?>fancybox/jquery.fancybox.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?php echo WS_URL ?>fancybox/helpers/jquery.fancybox-buttons.css?v=2.0.3" />
		
		<script type="text/javascript" src="<?php echo WS_URL ?>js/jquery-1.7.2.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/jquery.mousewheel-3.0.6.pack.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>fancybox/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>fancybox/helpers/jquery.fancybox-buttons.js?v=2.0.3"></script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				$("a.fancybox").fancybox({
					helpers: {
						title : {
							type : 'outside'
						},
						overlay : {
							speedIn : 500,
							opacity : 0.85
						}
					}
				});
			});
		</script>
		
		<?php Helper::extend_header() ?>

	</head>

	<body>

		<header>
		
			<ul id="nav">
				
				<li>
					<a href="<?php echo WS_URL ?>" target="_blank"><?php echo WS_NAME ?></a>
				</li>
			
				<li>
					<a href="<?php echo Url::_(array('ns' => 'dashboard')) ?>"><?php echo Lang::_('Dashboard') ?></a>
				</li>
				
				<li>
					<a href="<?php echo Url::_(array('ns' => 'network')) ?>"><?php echo Lang::_('Network') ?></a>
					
					<ul>
						<li><a href="<?php echo Url::_(array('ns' => 'network', 'ctl' => 'settings')) ?>"><?php echo Lang::_('Settings') ?></a></li>
					</ul>
				</li>
			
				<li>
					<a href="<?php echo Url::_(array('ns' => 'posts')) ?>"><?php echo Lang::_('Posts') ?></a>
					
					<ul>
						<li><a href="<?php echo Url::_(array('ns' => 'posts', 'ctl' => 'edit')) ?>"><?php echo Lang::_('Add') ?></a></li>
						<li>
							<a href="<?php echo Url::_(array('ns' => 'posts')) ?>"><?php echo Lang::_('Posts') ?></a>
							
							<ul>
								<li><a href="<?php echo Url::_(array('ns' => 'posts'), array('status' => 'publish')) ?>"><?php echo Lang::_('Published') ?></a></li>
								<li><a href="<?php echo Url::_(array('ns' => 'posts'), array('status' => 'draft')) ?>"><?php echo Lang::_('Draft') ?></a></li>
								<li><a href="<?php echo Url::_(array('ns' => 'posts'), array('status' => 'trash')) ?>"><?php echo Lang::_('Trash') ?></a></li>
							</ul>
						</li>
					</ul>
				</li>
			
				<li>
					<a href="<?php echo Url::_(array('ns' => 'media')) ?>"><?php echo Lang::_('Media') ?></a>
					
					<ul>
						<li>
							<a href="<?php echo Url::_(array('ns' => 'media', 'ctl' => 'add')) ?>"><?php echo Lang::_('Add') ?></a>
							
							<ul>
								<li><a href="<?php echo Url::_(array('ns' => 'media', 'ctl' => 'add'), array('type' => 'album')) ?>"><?php echo Lang::_('Album') ?></a></li>
								<li><a href="<?php echo Url::_(array('ns' => 'media', 'ctl' => 'add'), array('type' => 'linkage')) ?>"><?php echo Lang::_('Linkage') ?></a></li>
								<li><a href="<?php echo Url::_(array('ns' => 'media', 'ctl' => 'add'), array('type' => 'video')) ?>"><?php echo Lang::_('Video') ?></a></li>
							</ul>
						</li>
						<li>
							<a href="<?php echo Url::_(array('ns' => 'media')) ?>"><?php echo Lang::_('Media') ?></a>
							
							<ul>
								<li><a href="<?php echo Url::_(array('ns' => 'media')) ?>"><?php echo Lang::_('Image') ?></a></li>
								<li><a href="<?php echo Url::_(array('ns' => 'media'), array('type' => 'video')) ?>"><?php echo Lang::_('Video') ?></a></li>
								<li><a href="<?php echo Url::_(array('ns' => 'media'), array('type' => 'alien')) ?>"><?php echo Lang::_('External Video') ?></a></li>
							</ul>
						</li>
						<li><a href="<?php echo Url::_(array('ns' => 'media', 'ctl' => 'albums')) ?>"><?php echo Lang::_('Album') ?></a></li>
					</ul>
				</li>
			
				<li>
					<a href="<?php echo Url::_(array('ns' => 'comments')) ?>"><?php echo Lang::_('Comments') ?></a>
					
					<ul>
						<li><a href="<?php echo Url::_(array('ns' => 'comments')) ?>"><?php echo Lang::_('Pending') ?></a></li>
						<li><a href="<?php echo Url::_(array('ns' => 'comments'), array('status' => 'approved')) ?>"><?php echo Lang::_('Approved') ?></a></li>
						<li><a href="<?php echo Url::_(array('ns' => 'comments'), array('status' => 'spam')) ?>"><?php echo Lang::_('Spam') ?></a></li>
						<li><a href="<?php echo Url::_(array('ns' => 'comments'), array('status' => 'trash')) ?>"><?php echo Lang::_('Trash') ?></a></li>
					</ul>
				</li>
			
				<li>
					<a href="<?php echo Url::_(array('ns' => 'users', 'ctl' => 'profile')) ?>"><?php echo Lang::_('Profile') ?></a>
				</li>
				
				<li>
					<a href="<?php echo Url::_(array('ns' => 'plugins', 'ctl' => 'bridge')) ?>"><?php echo Lang::_('Plugins') ?></a>
					
					<?php 
					
						/*$plugins = Helper::plugins_infos();
						
						if(!empty($plugins)){
						
							echo '<ul id="mplg">';
							
							foreach($plugins as $plg)
								echo '<li><a href="index.php?ns='.$plg['namespace'].'&ctl='.$plg['entry_point'].'">'.$plg['name'].'</a></li>';
							
							echo '</ul>';
						
						}*/
					
					?>
					
				</li>
				
				<?php
				
					if($page->_user->_permissions->setting === true){
					
						echo '<li>'.
							 	'<a href="'.Url::_(array('ns' => 'settings')).'">'.Lang::_('Settings').'</a>'.
							 	'<ul>'.
							 		'<li><a href="'.Url::_(array('ns' => 'categories')).'">'.Lang::_('Categories').'</a></li>'.
							 		'<li>'.
							 			'<a href="'.Url::_(array('ns' => 'users')).'">'.Lang::_('Users').'</a>'.
							 			'<ul>'.
							 				'<li><a href="'.Url::_(array('ns' => 'users', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></li>'.
							 				'<li><a href="'.Url::_(array('ns' => 'roles')).'">'.Lang::_('Roles').'</a></li>'.
							 			'</ul>'.
							 		'</li>'.
							 		'<li><a href="'.Url::_(array('ns' => 'social')).'">'.Lang::_('Social Buttons').'</a></li>'.
							 		'<li><a href="'.Url::_(array('ns' => 'homepage')).'">'.Lang::_('Homepage').'</a></li>'.
							 		'<li>'.
							 			'<a href="'.Url::_(array('ns' => 'templates')).'">'.Lang::_('Templates').'</a>'.
							 			'<ul>'.
							 				'<li><a href="'.Url::_(array('ns' => 'templates', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></li>'.
							 				'<li><a href="'.Url::_(array('ns' => 'templates', 'ctl' => 'library')).'">'.Lang::_('Library').'</a></li>'.
							 			'</ul>'.
							 		'</li>'.
							 		'<li>'.
							 			'<a href="'.Url::_(array('ns' => 'plugins')).'">'.Lang::_('Plugins').'</a>'.
							 			'<ul>'.
							 				'<li><a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'add')).'">'.Lang::_('Add').'</a></li>'.
							 				'<li><a href="'.Url::_(array('ns' => 'plugins', 'ctl' => 'library')).'">'.Lang::_('Library').'</a></li>'.
							 			'</ul>'.
							 		'</li>'.
							 		'<li>'.
							 			'<a href="'.Url::_(array('ns' => 'links')).'">'.Lang::_('Links').'</a>'.
							 			'<ul>'.
							 				'<li><a href="'.Url::_(array('ns' => 'links', 'ctl' => 'edit'), array('action' => 'create')).'">'.Lang::_('Add').'</a></li>'.
							 			'</ul>'.
							 		'</li>'.
								 	'<li>'.
									 	'<a href="#">'.Lang::_('System').'</a>'.
									 	'<ul>'.
									 		'<li><a href="'.Url::_(array('ns' => 'activity')).'">'.Lang::_('Activity').'</a></li>'.
									 		'<li><a href="'.Url::_(array('ns' => 'update')).'">'.Lang::_('Update').'</a></li>'.
									 	'</ul>'.
							 	'</ul>'.
							 '<li>';
					
					}
				
				?>
				<li id="logout">
					<?php echo Lang::_('Hi').' '.$page->_user->_username ?> | 
					<a href="<?php echo Url::_(array('ns' => 'session', 'ctl' => 'logout')) ?>"><?php echo Lang::_('Logout') ?></a>
				</li>
			</ul>
			
		</header>
		
		<section id="wrapper">