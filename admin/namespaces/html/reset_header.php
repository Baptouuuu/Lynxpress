<?php 
	use \Library\Lang\Lang as Lang;
	
	defined('FOOTPRINT') or die();
?>
<!DOCTYPE html>
<html>
	
	<head>
	
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<title><?php echo $page->_title.' | '.WS_NAME ?></title>
		<link rel="index" href="<?php echo WS_URL ?>" title="<?php echo WS_NAME ?>" />
		<link rel="icon" type="image/png" href="<?php echo WS_URL ?>images/lynxpress-mini.png" />
		<link rel="stylesheet" type="text/css" href="<?php echo WS_URL ?>css/admin/main.css" />
		
		<script type="text/javascript" src="<?php echo WS_URL ?>js/jquery-1.7.2.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/admin/core/viewModel.messages.js"></script>
	
	</head>
	
	<body>
		
		<section id="wrapper">
			
			