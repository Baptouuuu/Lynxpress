<?php

	$menu = new \Library\Model\Setting('menu', '_key');
	$menu = json_decode($menu->_data);

?>
<!DOCTYPE html>
<html>

	<head>
	
		<meta charset="utf-8" />
		<meta name=viewport content="width=device-width, initial-scale=1, maximum-scale=1" />
		<title>404 Page Not Found | <?php echo WS_NAME ?></title>
		<link rel=index href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>" />
		<link rel=icon type="image/png" href="<?php echo WS_URL ?>images/lynxpress-mini.png" />
		<link rel=alternate type="application/rss+xml" title="<?php echo WS_NAME ?>" href="<?php echo \Library\Url\Url::_(array('ns' => 'rss')) ?>" />
		<link rel=stylesheet type="text/css" href="<?php echo WS_URL ?>css/templates/main/main.css" />
		<link rel=stylesheet type="text/css" href="<?php echo WS_URL ?>fancybox/jquery.fancybox.css" media="screen" />
		<link rel=stylesheet type="text/css" href="<?php echo WS_URL ?>fancybox/helpers/jquery.fancybox-buttons.css?v=2.0.3" />
	
	</head>
	
	<body>
	
		<header id=header>
		
			<div id=logo>
				<a href="<?php echo WS_URL ?>">
					<img src="<?php echo WS_URL ?>images/lynxpress_shadow.png" alt="Lynxpress" />
				</a>
			</div>
			
			<h2 id=brand>
				<a href="<?php echo WS_URL ?>"><?php echo WS_NAME ?></a>
			</h2>
			
			<?php \Template\Main\Master\Master::menu($menu) ?>
			
			<div id=toggle>
				<span></span>
				<span></span>
				<span></span>
			</div>
		
		</header>
		
		<section id=content>
		
			<h1 class=page_title>404 Page Not Found</h1>
			
			<div id=e404>
			
				<p>Oops! Lynxpress can't find what you're looking for.</p>
				
				<form method=get action="<?php echo WS_URL.((URL_REWRITING)?'search':'') ?>">
					<?php echo ((!URL_REWRITING)?'<input type=hidden name=ns value=search />':'') ?>
					<input class=input type=search name=s value="" placeholder="Try this search bar maybe..." list=searchElements x-webkit-speech />
				</form>
				
				<p>Otherwise there's still the menu bar ;).</p>
			
			</div>
		
		</section>
				
		<aside id=sidebar>
		
			
		
		</aside>
		
		<script type="text/javascript" src="<?php echo WS_URL ?>js/jquery-1.7.2.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/admin/core/app.server.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/admin/core/app.localStorage.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/templates/main/viewModel.search.js"></script>
	
	</body>

</html>