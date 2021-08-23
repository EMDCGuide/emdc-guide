<?php
use com\cminds\registration\addon\approvenewusers\model\Settings;
use com\cminds\registration\addon\approvenewusers\helper\HtmlHelper;
use com\cminds\registration\addon\approvenewusers\metabox\InvitationCodeApproveNewUsersBox;
?>
<div class="cmreganu-metabox-approve-new-users">
	<div class="cmvl-channel-sort">
		<p>Require admin approval for new users registration using this invitation code:</p>
		<div><?php echo HtmlHelper::renderSelect(InvitationCodeApproveNewUsersBox::FIELD_APPROVE, $options, $code->getApprovalStatus()); ?></div>
		<p>Global option is: <strong><?php echo (Settings::getOption(Settings::OPTION_APPROVE_REGISTRATION_ENABLE) ? 'enabled' : 'disabled'); ?></strong></p>
	</div>
</div>