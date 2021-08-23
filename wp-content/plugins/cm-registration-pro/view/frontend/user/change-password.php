<?php
use com\cminds\registration\model\User;
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;
$user = User::getUserData();
$hide_password_characters = Settings::getOption(Settings::OPTION_PASSWORD_HIDE_CHARS);
?>
<form action="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>" method="post" class="cmreg-form cmreg-change-password-form">
	<?php if ($atts['showheader']): ?>
		<h3 class="change_password_form_header"><?php echo Labels::getLocalized('change_password_form_header'); ?></h3>
	<?php endif; ?>
	<p>
		<label><?php echo Labels::getLocalized('change_password_new_pass_field'); ?></label>
		<input type="password" name="cmregef_password" value="" autocomplete="off" />
	</p>
	<p>
		<label><?php echo Labels::getLocalized('change_password_repeat_pass_field'); ?></label>
		<input type="password" name="cmregef_password_repeat" value="" autocomplete="off" />
	</p>
	<div class="form-summary">
		<input type="hidden" name="action" value="cmreg_change_password" />
		<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
		<input type="submit" value="<?php echo esc_attr(Labels::getLocalized('change_password_form_btn')); ?>" class="button button-primary" />
	</div>
</form>