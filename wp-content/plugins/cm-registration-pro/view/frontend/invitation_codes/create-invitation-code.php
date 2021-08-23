<?php
use com\cminds\registration\model\InvitationCode;
use com\cminds\registration\model\Labels;
?>
<div class="cmreg-create-invitation-code-shortcode">
	<form method="post" action="<?php echo esc_attr($url); ?>" data-action="<?php echo esc_attr($action);
		?>" data-nonce="<?php echo esc_attr($nonce); ?>" data-hash="<?php echo esc_attr($attsHash); ?>">
		<?php if ($atts['showparams']): ?>
			<div class="cmreg-invitation-params">
				<h3 class="create_invitation_code_params_header"><?php echo Labels::getLocalized('create_invitation_code_params_header'); ?></h3>
				<dl>
					<dt><?php echo Labels::getLocalized('invitation_expiration_date'); ?></dt>
						<dd><?php echo esc_html($atts['expiration'] ? $atts['expiration'] : Labels::getLocalized('invitation_expires_never')); ?></dd>
					<dt><?php echo Labels::getLocalized('invitation_users_limit'); ?></dt>
						<dd><?php echo esc_html($atts['userslimit'] ? $atts['userslimit'] : Labels::getLocalized('invitation_unlimited_users')); ?></dd>
					<dt><?php echo Labels::getLocalized('invitation_email_verification'); ?></dt>
						<dd><?php echo esc_html($verifyEmail ? Labels::getLocalized('invitation_verify_email_required') : Labels::getLocalized('invitation_verify_email_not_required')); ?></dd>
					<dt><?php echo Labels::getLocalized('invitation_user_role'); ?></dt>
						<dd><?php echo esc_html($userRoleName); ?></dd>
				</dl>
			</div>
		<?php endif; ?>
		<?php if (!empty($atts['emailinput'])): ?>
			<dl>
				<dt><?php echo Labels::getLocalized('invitation_send_to_email'); ?></dt>
				<dd><input type="email" name="email"></dd>
			</dl>
		<?php endif; ?>
		<input type="hidden" name="showlink" value="<?php echo isset($atts['showlink'])?$atts['showlink']:0; ?>" />
		<div class="clear"></div>
		<p><input type="submit" class="cmreg-create-invitation-code-btn" value="<?php echo esc_attr(Labels::getLocalized('invitation_create_btn')); ?>"></p>
	</form>
</div>