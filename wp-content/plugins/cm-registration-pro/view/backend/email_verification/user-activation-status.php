<h3>CM Registration Email Verification</h3>
<table class="form-table">
	<tbody>
		<tr class="cmreg-user-activation-status">
			<th valign="top">Activation Status</th>
			<td>
				<?php if ($canLogin): ?>
					<p><strong>Activated</strong></p>
				<?php else: ?>
				<p><strong>Inactive</strong></p>

				<p><a href="#" class="button cmreg-manually-activation" data-user-id="<?php echo esc_attr($userId);
					?>" data-nonce="<?php echo esc_attr($nonce); ?>">Manually activate</a></p>
				<p class="cmreg-manually-activation-response"></p>

				<p><a href="#" class="button cmreg-resend-activation-email" data-user-id="<?php echo esc_attr($userId);
					?>" data-nonce="<?php echo esc_attr($nonce); ?>">Resend the activation email</a></p>
				<p class="cmreg-resend-activation-email-response"></p>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>