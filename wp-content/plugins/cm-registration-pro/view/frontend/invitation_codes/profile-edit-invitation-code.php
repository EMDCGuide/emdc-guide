<?php
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\InvitationCode;
?>
<div class="cmreg-registration-field cmreg-invitation-code">
	<span class="cmreg-field-label"><?php echo Labels::getLocalized('user_profile_invit_code'); ?></span>
	<?php
	$current_user_can = current_user_can('manage_options');
	$readonly = ''; if($current_user_can == "0") { $readonly = 'readonly="readonly"'; }
	?>
	<?php if ($code): ?>
		<input type="text" name="invitation_code" value="<?php echo esc_attr($code->getCodeString()); ?>" <?php echo $readonly; ?> />
		<span class="cmreg-field-description"><?php echo esc_html($code->getTitle()); ?></span>
	<?php else: ?>
		<input type="text" name="invitation_code" value="" <?php echo $readonly; ?> />
		<span class="cmreg-field-description"><?php echo Labels::getLocalized('user_profile_no_invit_code'); ?></span>
	<?php endif; ?>
</div>