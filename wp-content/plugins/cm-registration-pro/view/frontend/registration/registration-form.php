<?php
use com\cminds\registration\controller\RegistrationController;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\Labels;
$redirectUrl = Settings::getOption(Settings::OPTION_REGISTER_REDIRECT_URL);
if(esc_attr($atts['redirect-to']) != '') {
	if(strpos(esc_attr($atts['redirect-to']), "http") !== false) {
		$redirectUrl = esc_attr($atts['redirect-to']);
	} else {
		$redirectUrl = site_url().esc_attr($atts['redirect-to']);
	}
}
$cmreg_redirect_url = filter_input(INPUT_GET, 'cmreg_redirect_url');
if($cmreg_redirect_url != '') {
	$redirectUrl = $cmreg_redirect_url;
}
?>
<div class="cmreg-registration cmreg-wrapper">
	<form method="post" data-ajax-url="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>" class="cmreg-form cmreg-registration-form">
		
		<h2><?php echo Labels::getLocalized('register_form_header'); ?></h2>
		<div class="cmreg-form-text"><?php echo Labels::getLocalized('register_form_text'); ?></div>
		
		<?php do_action('cmreg_register_form', 'cmreg_overlay', $atts); ?>
		
		<div class="cmreg-buttons-field">
			<input type="hidden" name="action" value="cmreg_registration" />
			<input type="hidden" name="cmreg_redirect_url" value="<?php echo esc_attr($redirectUrl); ?>" />
			<input type="hidden" name="<?php echo esc_attr(RegistrationController::FIELD_ROLE); ?>" value="<?php echo esc_attr($atts['role']); ?>" />
			<input type="hidden" name="<?php echo esc_attr(RegistrationController::FIELD_ROLE_NONCE); ?>" value="<?php echo esc_attr($roleNonce); ?>" />
			<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
			<button type="submit"><span class="dashicons dashicons-edit"></span><?php echo Labels::getLocalized('register_form_submit_btn'); ?></button>
		</div>
		
		<?php do_action('cmreg_register_form_bottom', $atts); ?>
		
		<?php if (isset($atts['login-url'])): ?>
			<div class="cmreg-login-link"><a href="<?php echo esc_attr($atts['login-url']); ?>"><?php
				echo (isset($atts['login-link']) ? $atts['login-link'] : Labels::getLocalized('registration_login_btn')); ?></a></div>
		<?php endif; ?>
		
	</form>
</div>