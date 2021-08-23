<?php
use com\cminds\registration\model\User;
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;
$hide_password_characters = Settings::getOption(Settings::OPTION_PASSWORD_HIDE_CHARS);
if(isset($_GET['key']) && isset($_GET['login'])) {
	?>
	<form action="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>" method="post" class="cmreg-form cmreg-reset-password-form">
		<?php if ($atts['showheader']): ?>
			<h3 class="reset_password_form_header"><?php echo Labels::getLocalized('reset_password_form_header'); ?></h3>
		<?php endif; ?>
		<p>
			<label><?php echo Labels::getLocalized('reset_password_new_pass_field'); ?></label>
			<input type="password" name="cmregreset_password" value="" autocomplete="off" />
			<input type="hidden" name="rp_key" value="<?php echo isset($_GET['key'])?$_GET['key']:''; ?>" />
			<input type="hidden" name="user_login" value="<?php echo isset($_GET['login'])?$_GET['login']:''; ?>" />
		</p>
		<div class="form-summary">
			<input type="hidden" name="action" value="cmreg_reset_password" />
			<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
			<input type="submit" value="<?php echo esc_attr(Labels::getLocalized('reset_password_form_btn')); ?>" class="button button-primary" />
		</div>
	</form>
	<?php
}
?>