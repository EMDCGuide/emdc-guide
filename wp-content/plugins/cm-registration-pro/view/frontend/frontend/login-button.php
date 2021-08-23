<?php
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;

$redirect_url = '';
if(isset($atts['redirect-to']) && esc_attr($atts['redirect-to']) != '') {
	if(strpos(esc_attr($atts['redirect-to']), "http") !== false) {
		$redirect_url = esc_attr($atts['redirect-to']);
	} else {
		$redirect_url = site_url().esc_attr($atts['redirect-to']);
	}
}
if(is_user_logged_in()) {
	$user_info = get_userdata(get_current_user_id());
	$display_name = $user_info->display_name;
	?>
	<h2 class="login-button-hi">
		<?php
		echo str_replace('{display_name}', $display_name, Labels::getLocalized('you_are_logged_in'));
		?>
	</h2>
	<?php
}
$loginAuthenticationPopup = Settings::getOption(Settings::OPTION_LOGIN_AUTHENTICATION_POPUP);
if($loginAuthenticationPopup == '1') {
	$cls = 'cmreg-only-login-click';
} else if($loginAuthenticationPopup == '2') {
	$cls = 'cmreg-only-registration-click';
} else {
	$cls = 'cmreg-login-click';
}
?>
<a href="/navigator/" class="cmreg-navigator-button">Access NAVIGATOR</a>
<a href="<?php echo esc_attr($href); ?>" class="cmreg-login-button <?php echo $cls; ?><?php if($extraClass != '') { echo ' '.$extraClass; } ?>" redirect_to="<?php echo $redirect_url; ?>" after_login="<?php echo $atts['after-login']; ?>" after_text="<?php echo $atts['after-text']; ?>"><?php echo $loginButtonText; ?></a>