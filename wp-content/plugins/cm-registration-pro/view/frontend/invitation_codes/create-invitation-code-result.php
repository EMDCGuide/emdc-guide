<?php
use com\cminds\registration\model\InvitationCode;
use com\cminds\registration\model\Labels;
?>
<div class="cmreg-create-invitation-code-result">
	<p>Invitation code:</p>
	<div class="cmreg-invitation-code-string"><?php echo $codeString; ?></div>
	<?php //if ($sentByEmail): ?>
		<p class="cmreg-invitation-code-sent-msg">The invitation link has been send to the specified email address.</p>
	<?php //endif; ?>
	<?php
	if($showlink == '1') {
		?>
		<p class="cmreg-invitation-code-link">
			Also You can share following invitation link with client:
			<br>
			<?php echo $link; ?>
		</p>
		<?php
	}
	?>
</div>