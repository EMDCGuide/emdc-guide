<?php
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;
?>
<div class="cmreg-overlay">
	<div class="cmreg-overlay-inner">
		<?php
		$isUserLoggedIn = intval(is_user_logged_in());
		$loginAuthenticationPopupEnable = (isset($atts['post_page_id']))?get_post_meta($atts['post_page_id'], 'cmreg_login_access', true):'0';
		$loginAuthenticationPopupForce = Settings::getOption(Settings::OPTION_LOGIN_AUTHENTICATION_POPUP_FORCE);

		if($isUserLoggedIn == '0' && $loginAuthenticationPopupEnable > 0 && $loginAuthenticationPopupForce == '0') {

		} else {
			?>
			<span class="cmreg-overlay-close" title="<?php echo esc_attr(Labels::getLocalized('close')); ?>">&times;</span>
			<?php
		}
		?>
		<?php echo $content; ?>
	</div>
</div>