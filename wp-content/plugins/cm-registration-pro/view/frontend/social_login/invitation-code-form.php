<?php
use com\cminds\registration\model\Labels;
?>
<form class="cmreg-form cmreg-social-login-invitcode-form" action="<?php echo esc_attr($actionUrl);
		?>" data-nonce="<?php echo esc_attr($nonce); ?>" data-cache-key="<?php echo esc_attr($cacheKey); ?>" method="post">
	<p class="cmreg-social-login-invit-code-text"><?php echo Labels::getLocalized('social_login_invit_code_text'); ?></p>
	<p><label>
		<input type="text" name="invitation_code" placeholder="<?php echo esc_attr(Labels::getLocalized('social_login_invit_code_input'));
			?>" class="cmreg-invitation-code-field" <?php echo ($invitationCodeRequired ? 'required' : ''); ?>>
		<input type="submit" value="<?php echo esc_attr(Labels::getLocalized('social_login_invit_code_ok_btn')); ?>">
	</label>
	<p>
		<?php if (!$invitationCodeRequired): ?>
			<input type="submit" value="<?php echo esc_attr(Labels::getLocalized('social_login_no_invit_code_btn')); ?>" class="cmreg-no-invit-code-btn">
		<?php endif; ?>
	</p>
</form>