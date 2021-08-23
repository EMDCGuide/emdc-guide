<?php

?>


<?php if (!empty($message)): ?>
	<div id="message" class="updated"><p><?php echo $message; ?></p></div>
<?php endif; ?>


<?php if (empty($users)): echo '<p>No users to approve.</p>'; ?>
<?php else: ?>

<table class="cmvl-report-table wp-list-table widefat fixed">
	
	<thead><tr>
		<th>Login</th>
		<th>Display name</th>
		<th>Email</th>
		<th class="cmvl-narrow">Registration date</th>
		<th class="cmvl-narrow">Actions</th>
	</tr></thead>
	
	<tbody><?php foreach ($users as $user): ?>
	
		<tr>
			
			<td class="cmvl-title-col">
				<a href="<?php echo esc_attr($user->getEditUrl()); ?>"><?php echo esc_html($user->getLogin()); ?></a>
			</td>
			<td><?php echo esc_html($user->getDisplayName()); ?></td>
			<td><?php echo esc_html($user->getEmail()); ?></td>
			<td><?php echo esc_html($user->getRegistrationDate()); ?></td>
			<td>
				<a href="<?php echo esc_attr($user->getAccountApproveUrl($currentUrl)); ?>" class="button">Approve</a>
				<a href="<?php echo esc_attr($user->getAccountRejectUrl($currentUrl)); ?>" class="button">Reject</a>
			</td>
			
		</tr>
		
	<?php endforeach; ?></tbody>
		
</table>
	
<?php endif; ?>