<?php 
	
	use \Template\Main\Main as Template;
	use \Library\Url\Url;
	
	defined('FOOTPRINT') or die();

?>
		</section>
		
		<aside id=sidebar>
		
			<?php $page->display_sidebar() ?>
		
		</aside>
		
		<footer id=footer>
		
			<div id=fpub>powered by <a href="http://lynxpress.org" target=_blank>lynxpress</a></div>
			
			<div id=fright>
				
				<a id=fcontact>Contact</a> - <a href="<?php echo Url::_(array('ns' => 'rss')) ?>">RSS</a>
				
			</div>
		
		</footer>
		
		<div id=contact>
		
			<div id=contact_form data-url="<?php echo Url::_(array('ns' => 'contact', 'ctl' => 'ajax')) ?>">
				<h3>Contact</h3>
				<input class=input type=email name=cemail placeholder="my@email.org" required /><br/>
				<input class=input type=text name=cobject placeholder="Subject" required /><br/>
				<textarea name=ccontent required></textarea><br/>
				<button class=button>Submit</button>
			</div>
			<div id=contact_bg></div>
		
		</div>
		
		<input id=search_datalist_url type=hidden data-url="<?php echo Url::_(array('ns' => 'api', 'ctl' => 'posts'), array(), true) ?>" />
		
		<script type="text/javascript" src="<?php echo WS_URL ?>js/jquery-1.7.2.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/jquery.mousewheel-3.0.6.pack.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>fancybox/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>fancybox/helpers/jquery.fancybox-buttons.js?v=2.0.3"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>photoswipe/klass.min.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>photoswipe/code.photoswipe-3.0.4.min.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/admin/core/app.server.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/templates/main/viewModel.contact.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/admin/core/app.localStorage.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/templates/main/viewModel.search.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/admin/core/viewModel.toggleMenu.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				
				var width = $(document.body).width();
				
				if(width <= 1280){
				
					try{
					
						$('[data-rel="lightbox"]').photoSwipe({
							enableMouseWheel: false, 
							enableKeyboard: false 
						});
					
					}catch(e){
					
						console.log(e);
					
					}
				
				}else{
					
					try{
					
						$('[data-rel="lightbox"]').fancybox({
							prevEffect: 'none',
							nextEffect: 'none',
							closeBtn:	false,
							helpers: {
								title : {
									type : 'outside'
								},
								overlay : {
									speedIn : 500,
									opacity : 0.85
								},
								buttons: {}
							}
						});
					
					}catch(e){
					
						console.log(e);
					
					}
				
				}
			});
		</script>
		
		<?php Template::render_js() ?>
	
	</body>

</html>