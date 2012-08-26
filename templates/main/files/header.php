<?php 
	
	use \Template\Main\Main as Template;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();

?>
<!DOCTYPE html>
<html>

	<head>
	
		<meta charset="utf-8" />
		<meta name=viewport content="width=device-width, initial-scale=1, maximum-scale=1" />
		<meta name=generator content=Lynxpress />
		<title><?php echo $page->_title.' | '.WS_NAME ?></title>
		<link rel=index href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>" />
		<link rel=icon type="image/png" href="<?php echo WS_URL ?>images/lynxpress-mini.png" />
		<link rel=alternate type="application/rss+xml" title="<?php echo WS_NAME ?>" href="<?php echo Url::_(array('ns' => 'rss')) ?>" />
		<link rel=stylesheet type="text/css" href="<?php echo WS_URL ?>css/templates/main/main.css" />
		<link rel=stylesheet type="text/css" href="<?php echo WS_URL ?>fancybox/jquery.fancybox.css" media="screen" />
		<link rel=stylesheet type="text/css" href="<?php echo WS_URL ?>fancybox/helpers/jquery.fancybox-buttons.css?v=2.0.3" />
		<link rel=stylesheet type="text/css" href="<?php echo WS_URL ?>photoswipe/photoswipe.css" />
		
		<?php Template::render_css() ?>
	
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
			
			<?php $page->display_menu() ?>
			
			<div id=toggle>
				<span></span>
				<span></span>
				<span></span>
			</div>
		
		</header>
		
		<section id=content>
		
			