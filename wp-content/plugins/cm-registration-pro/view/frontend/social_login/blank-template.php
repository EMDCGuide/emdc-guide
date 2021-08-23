<?php
use com\cminds\registration\model\Labels;
get_header();
?>
<div id="main-content">
	<div class="container">
		<div id="content-area" class="clearfix" style="padding-top:20px;padding-bottom:50px;margin: 0 auto;max-width: 800px;text-align: center;font:normal 14px Arial;">
			<?php echo $content; ?>
			<p><a href="<?php echo esc_attr(site_url()); ?>"><?php echo Labels::getLocalized('social_login_back_to_page'); ?></a></p>
		</div>
	</div>
</div>
<?php get_footer(); ?>
</body>
</html>