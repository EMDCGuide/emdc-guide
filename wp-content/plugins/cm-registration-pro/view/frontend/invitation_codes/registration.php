<?php
use com\cminds\registration\controller\InvitationCodesController;
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;
?>
<div class="cmreg-invitation-code-field" data-input-visible="<?php echo intval($invitationCodeRequired OR !empty($invitationCode)); ?>">

	<a href="" class="cmreg_ainvlink"><?php echo Labels::getLocalized('register_invitation_code_link'); ?></a>
	
	<div class="cmreg_ainvlink_con">
		<?php if (Settings::getOption(Settings::OPTION_FORM_FIELD_LABEL_ENABLE)): ?>
			<label class="cmreg-label">
				<?php
				if($invitationCodeLabel == '') {
					echo esc_attr(Labels::getLocalized('field_invitation_code'));
				} else {
					echo esc_attr(Labels::getLocalized($invitationCodeLabel));
				}
				if($invitationCodeRequired) {
					echo ' '.Labels::getLocalized('field_required');
				}
				?>
			</label>
		<?php endif; ?>

		<input type="text" class="text" name="<?php echo InvitationCodesController::FIELD_INVITATION_CODE; ?>" <?php echo ($invitationCodeRequired ? 'required' : ''); ?> placeholder="<?php echo Labels::getLocalized($invitationCodePlaceholder); ?>" value="<?php echo $invitationCode; ?>" />

		<?php if($invitationCodeTooltip != '') { ?>
			<span class="cmreg-field-description"><?php echo $invitationCodeTooltip; ?></span>
		<?php } ?>
	</div>

</div>