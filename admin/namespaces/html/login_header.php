<?php 
	use \Library\Lang\Lang; 
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();
?>
<!DOCTYPE html>
<html>
	
	<head>
	
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<title><?php echo Lang::_('Connection to', 'session').' | '.WS_NAME ?></title>
		<link rel="index" href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>" />
		<link rel="icon" type="image/png" href="<?php echo WS_URL ?>images/lynxpress-mini.png" />
		<link rel="stylesheet" type="text/css" href="<?php echo WS_URL ?>css/admin/main.css" />
		
		<script type="text/javascript" src="<?php echo WS_URL ?>js/jquery-1.7.2.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/admin/core/viewModel.messages.js"></script>
	
	</head>
	
	<body>
		
		<section id="wrapper">
			
			<form method="post" action="<?php echo Url::_(array('ns' => 'session', 'ctl' => 'login')) ?>">
			
				<div id="login">
				
					<div id="login_logo">
						<img src="<?php echo WS_URL ?>images/lynxpress_shadow.png" alt="Lynxpress" />
					</div>
					
					<div id="login_form">
						
						<?php echo $page->_action_msg ?>
								
						<input class="input" type="text" name="username" placeholder="Username" autofocus required /><br />
						<input class="input" type="password" name="password" placeholder="Password" required /><br />
						<input class="button" type="submit" name="login" value="<?php echo Lang::_('Connection', 'session') ?>" />
					
					</div>
					
					<div id="login_footer">
					
						<a href="<?php echo WS_URL ?>"><?php echo Lang::_('Back to', 'session').' '.WS_NAME ?></a> | <a href="<?php echo Url::_(array('ns' => 'users', 'ctl' => 'reset')) ?>"><?php echo Lang::_('Lost password?', 'session') ?></a>
					
					</div>
				
				</div>
			
			</form>
		
		</section>