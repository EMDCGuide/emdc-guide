<div class="cmreg-option-login-redirect-role">
	<?php foreach ($roles as $role => $label): ?>
		<div class="cmreg-role-item">
			<div class="cmreg-role-name"><?php echo $label; ?></div>
			<div class="cmreg-role-redirect"><input type="text" name="<?php echo esc_attr("{$optionName}[$role]");
				?>" value="<?php echo esc_attr(isset($value[$role]) ? $value[$role] : ''); ?>"></div>
		</div>
	<?php endforeach; ?>
</div>