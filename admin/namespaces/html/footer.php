<?php 

	use \Admin\Master\Helpers\Html as Helper;

	defined('FOOTPRINT') or die(); 

?>

		</section>
		
		<script type="text/javascript" src="<?php echo WS_URL ?>js/jquery-1.7.2.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/jquery.mousewheel-3.0.6.pack.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>fancybox/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>fancybox/helpers/jquery.fancybox-buttons.js?v=2.0.3"></script>
		<script type="text/javascript" src="<?php echo WS_URL ?>js/admin/core/viewModel.messages.js"></script>
		
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
		
		<?php Helper::extend_document('js') ?>
		
	</body>
</html>