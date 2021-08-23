<?php
use com\cminds\registration\model\ProfileField;
use com\cminds\registration\model\Labels;
?>
<h3>CM Registration Profile Fields</h3>
<table class="form-table">
	<tbody>
		<tr class="cmreg-profile-field">
			<th valign="top">
				<?php echo Labels::getLocalized('field_organization')!=''?Labels::getLocalized('field_organization'):'Organization'; ?>
			</th>
			<td>
				<?php
				$organization = get_user_meta($userId, 'organization', true);
				if(is_numeric($organization)) {
					if(current_user_can('administrator')) {
						echo '<a href="'.get_edit_post_link($organization).'" target="_blank">'.get_the_title($organization).'</a>';
					} else {
						echo get_the_title($organization);
					}
				} else {
					echo $organization;
				}
				?>
			</td>
		</tr>
		<?php foreach ($fields as $field): ?>
			<?php if ($field->getRegistrationFormRole()) continue; ?>
			<tr class="cmreg-profile-field">
				<th valign="top"><?php echo $field->getLabel(); ?></th>
				<td>
					<?php
					$value = $field->getValueForUser($userId);
					if (is_array($value)) $value = implode(', ', $value);
					echo esc_html($value);
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>