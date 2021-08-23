<?php
use com\cminds\registration\controller\LoginController;
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\ProfileField;

$hide_password_characters = Settings::getOption(Settings::OPTION_PASSWORD_HIDE_CHARS);

$loginField = Settings::getOption(Settings::OPTION_LOGIN_FIELD);
$loginFieldLabel = Labels::getLocalized('login_field_' . $loginField);
$loginFieldType = ($loginField == Settings::LOGIN_FIELD_EMAIL ? 'email' : 'text');
$redirectUrl = '';
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

$show_password = $atts['show-password'];
$two_step = $atts['two-step'];
?>
<div class="cmreg-login cmreg-wrapper">
	
	<?php
	$validationClass = '';
	if($two_step == '1') {
		$validationClass = ' need-validation';
	}
	?>

	<form method="post" data-ajax-url="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>" class="cmreg-form cmreg-login-form">

		<h2><?php echo Labels::getLocalized('login_form_header'); ?></h2>

		<div class="cmreg-form-text"><?php echo Labels::getLocalized('login_form_text'); ?></div>

		<div class="cmreg-login-field">
			<?php if (Settings::getOption(Settings::OPTION_FORM_FIELD_LABEL_ENABLE)): ?>
				<label class="cmreg-label"><?php echo $loginFieldLabel.' '.Labels::getLocalized('field_required'); ?></label>
			<?php endif; ?>
			<input type="<?php echo $loginFieldType; ?>" class="text" name="login" required placeholder="<?php
			echo esc_attr($loginFieldLabel); ?>" />
		</div>
		
		<?php
		if($show_password == '1') {
			?>
			<div class="cmreg-password-field">
				<?php if (Settings::getOption(Settings::OPTION_FORM_FIELD_LABEL_ENABLE)): ?>
					<label class="cmreg-label"><?php echo esc_attr(Labels::getLocalized('field_password')).' '.Labels::getLocalized('field_required'); ?></label>
				<?php endif; ?>
				
				<?php
				/*
				<input type="password" class="text" name="<?php echo esc_attr(ProfileField::REGISTRATION_FORM_ROLE_PASSWORD); ?>" required placeholder="<?php echo esc_attr(Labels::getLocalized('field_password')); ?>" />
				*/
				?>

				<div class="cmreg-password-block<?php echo $hide_password_characters == 1 ? ' show_as_password' : ''; ?>">
                    <input type="<?php echo $hide_password_characters == 1 ? 'password' : 'text'; ?>" name="<?php echo esc_attr(ProfileField::REGISTRATION_FORM_ROLE_PASSWORD); ?>" class="text cmreg_accesscode_value" placeholder="<?php echo esc_attr(Labels::getLocalized('field_password')); ?>" />
                    <?php if($hide_password_characters == 1) { ?>
                        <a href="javascript:void(0);" class="cmreg-input-type-trigger"><span class="dashicons dashicons-hidden"></span></a>
                    <?php } ?>
                </div>

			</div>
			<?php
		}
		?>

		<div class="cmreg-2fa-field" style="display:none;">
			<?php if (Settings::getOption(Settings::OPTION_FORM_FIELD_LABEL_ENABLE)): ?>
				<label class="cmreg-label">One Time Password (i.e. 2FA) <?php echo Labels::getLocalized('field_required'); ?></label>
			<?php endif; ?>
			<input type="text" class="text" name="two_factor_code" placeholder="One Time Password (i.e. 2FA)" />
		</div>

		<?php
		$toc = Settings::getOption(Settings::OPTION_LOGIN_TERMS_OF_SERVICE_CHECKBOX_TEXT);
		?>
		<div class="cmreg-login-toc-field<?php echo $validationClass; ?>">
			<?php
			if (strlen(strip_tags($toc)) > 0) {
				?>
				<label><input type="checkbox" name="cmreg_toc" style="vertical-align:initial;" required /> <?php echo $toc; ?></label>
				<?php
			}
			?>
		</div>

		<?php if (Settings::getOption(Settings::OPTION_LOGIN_REMEMBER_ENABLE)): ?>
			<div class="cmreg-remember-field"><label><input type="checkbox" name="remember" value="1" /> <?php
				echo Labels::getLocalized('login_form_remember'); ?></label></div>
		<?php endif; ?>

		<?php do_action('login_form', 'cmreg_overlay'); ?>

		<div class="cmreg-buttons-field">
			<input type="hidden" name="action" value="cmreg_login" />
			<input type="hidden" name="cmreg_redirect_url" value="<?php echo esc_attr($redirectUrl); ?>" />
			<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
			<button class="cmreg-login-submit-button" type="submit">
				<span class="dashicons dashicons-admin-users"></span>
				<?php echo Labels::getLocalized('login_form_submit_btn'); ?>
			</button>
		</div>

		<?php if (!Settings::getOption(Settings::OPTION_PREVENT_CALLING_LOGIN_FOOTER_FRONTEND)): ?>
			<?php do_action('login_footer'); ?>
		<?php endif; ?>

		<?php do_action('cmreg_login_form_bottom', $atts); ?>

	</form>

	<?php if (Settings::getOption(Settings::OPTION_LOGIN_LOST_PASSWORD_ENABLE)): ?>
		<div class="cmreg-lost-password-link"><a href=""><?php echo Labels::getLocalized('lost_pass_btn'); ?></a></div>
		<?php echo LoginController::getLostPasswordView(); ?>
	<?php endif; ?>
	
	<?php if (Settings::getOption(Settings::OPTION_SELF_REGISTER_ENABLE)): ?>
		<div class="cmreg-self-register-link"><a href=""><?php echo Labels::getLocalized('self_register_btn'); ?></a></div>
		<?php echo LoginController::getSelfRegisterView(); ?>
	<?php endif; ?>

	<?php if (isset($atts['registration-url'])): ?>
		<div class="cmreg-registration-link"><a href="<?php echo esc_attr($atts['registration-url']); ?>"><?php
			echo (isset($atts['registration-link']) ? $atts['registration-link'] : Labels::getLocalized('login_registration_btn')); ?></a></div>
	<?php endif; ?>

	<?php do_action('cmreg_login_wrapper_bottom', $atts); ?>

</div>
<?php
if(!is_admin()) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
if($two_step == '1' && is_plugin_active('cm-secure-login/plugin.php') || is_plugin_active('cm-secure-login-pro/plugin.php')) { ?>
<style>
.cmreg-wrapper div.cmreg-login-toc-field { margin-bottom:0; }
.cmlog-email-code-field label { display:none; }
.cmlog-email-code-field input[type="text"] { display:none; }
.cmlog-email-code-field span.otp_text { display:none; text-align:left; margin-top:10px; }
.cmreg-login-submit-button { display:none; }
</style>
<?php } ?>