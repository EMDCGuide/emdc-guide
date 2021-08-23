<?php
use com\cminds\registration\model\InvitationCode;
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\User;
?>
<div class="cmreg-list-users-invitations-shortcode">
	<?php if (!empty($codes)): ?>
		<table>
			<thead><tr>
				<th data-col="code"><?php echo Labels::getLocalized('invitation_code_str'); ?></th>
				<th data-col="email-addr"><?php echo Labels::getLocalized('invitation_email_addr'); ?></th>
				<th data-col="user-role"><?php echo Labels::getLocalized('invitation_user_role'); ?></th>
				<th data-col="expiration-date"><?php echo Labels::getLocalized('invitation_expiration_date'); ?></th>
				<th data-col="users-limit"><?php echo Labels::getLocalized('invitation_users_limit'); ?></th>
				<th data-col="email-verification"><?php echo Labels::getLocalized('invitation_email_verification'); ?></th>
				<th data-col="created-date"><?php echo Labels::getLocalized('invitation_created_date'); ?></th>
				<th data-col="used"><?php echo Labels::getLocalized('invitation_used'); ?></th>
			</tr></thead>
			<tbody><?php foreach ($codes as $code): ?>
				<?php
					if($atts['used'] == 'yes' && $code->getUsersCount() == 0) {
						continue;
					}
					if($atts['used'] == 'no' && $code->getUsersCount() > 0) {
						continue;
					}
				?>
				<tr>
					<td data-col="code"><?php echo esc_html($code->getCodeString()); ?></td>
					<td data-col="email-addr"><?php echo esc_html($code->getRequiredEmail()); ?></td>
					<td data-col="user-role"><?php $role = $code->getUserRole(); echo esc_html(User::getRoleNameByKey($role ? $role : $defaultRole)); ?></td>
					<td data-col="expiration-date"><?php $date = $code->getExpirationDateFormatted(); echo esc_html($date ? $date : Labels::getLocalized('invitation_expires_never')); ?></td>
					<td data-col="users-limit"><?php $limit = $code->getUsersLimit(); echo esc_html($code->getUsersCount() .'/'. ($limit ? $limit : Labels::getLocalized('invitation_unlimited_users'))); ?></td>
					<td data-col="email-verification"><?php echo esc_html($code->getEmailVerificationStatusOrGlobal() ? Labels::getLocalized('invitation_verify_email_required') : Labels::getLocalized('invitation_verify_email_not_required')); ?></td>
					<td data-col="created-date"><?php echo esc_html($code->getCreatedDate()); ?></td>
					<td data-col="used"><?php echo ($code->getUsersCount() > 0 ? Labels::getLocalized('invitation_used_yes') : Labels::getLocalized('invitation_used_no')); ?></td>
				</tr>
			<?php endforeach; ?></tbody>
		</table>
	<?php else: ?>
		<p><?php echo Labels::getLocalized('invitation_list_empty'); ?></p>
	<?php endif; ?>
</div>