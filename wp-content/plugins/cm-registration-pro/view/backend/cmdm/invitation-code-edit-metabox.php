<?php
use com\cminds\registration\model\InvitationCode;
?>
<p class="cmreg-cmdm-user-group">
Assign user to CMDM User Group: <select name="<?php echo InvitationCode::META_CMDM_USER_GROUP; ?>">
	<option value="">-- do not assign --</option>
	<?php foreach ($groups as $ttId => $groupName): ?>
		<?php printf('<option value="%s"%s>%s</option>',
				esc_attr($ttId),
				selected($ttId, $code->getCMDMUserGroup(), false),
				esc_html($groupName)
		); ?>
	<?php endforeach; ?>
	</select>
</p>