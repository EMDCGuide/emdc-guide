<?php
use com\cminds\registration\controller\LoginController;
use com\cminds\registration\model\Labels;
$selfRegisterNonce = wp_create_nonce(LoginController::SELF_REGISTER_NONCE);
?>
<form class="cmreg-self-register-form" data-ajax-url="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>">
	<h4><?php echo esc_attr(Labels::getLocalized('self_register_header')); ?></h4>
	<div class="cmreg-login-field">
		<input type="text" class="text" name="first_name" required placeholder="<?php echo esc_attr(Labels::getLocalized('self_register_first_name')); ?>" value="" />
	</div>
	<div class="cmreg-login-field">
		<input type="text" class="text" name="last_name" required placeholder="<?php echo esc_attr(Labels::getLocalized('self_register_last_name')); ?>" value="" />
	</div>
	<div class="cmreg-login-field">
		<input type="email" class="text" name="email" required placeholder="<?php echo esc_attr(Labels::getLocalized('self_register_email')); ?>" value="" />
	</div>
	<div class="cmreg-login-field">
		<input type="text" class="text" name="phone" placeholder="<?php echo esc_attr(Labels::getLocalized('self_register_phone')); ?>" value="" />
	</div>
	<div class="cmreg-login-field">
		<?php
		/* maxlength="4" pattern="\d{4}" */
		?>
		<input type="text" class="text" name="social_security" required placeholder="<?php echo esc_attr(Labels::getLocalized('self_register_social_security')); ?>" value="" />
	</div>
	<div class="cmreg-self-register-fieldset">
		<input type="hidden" name="action" value="cmreg_self_register" />
		<input type="hidden" name="nonce" value="<?php echo $selfRegisterNonce; ?>" />
		<button type="submit">
			<span class="dashicons dashicons-admin-users"></span>
			<?php echo Labels::getLocalized('self_register_registration_btn'); ?>
		</button>
	</div>
</form>