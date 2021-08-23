<?php
use com\cminds\siteaccessrestriction\view\SettingsView;
use com\cminds\siteaccessrestriction\model\Labels;

$settingsView = new SettingsView();
$labelsByCategories = Labels::getLabelsByCategories();
foreach ($labelsByCategories as $category => $labels):
	?><table><caption><?php echo (empty($category) ? 'Other' : $category); ?></caption><?php
	foreach ($labels as $key):
		if ($default = Labels::getDefaultLabel($key)) :
			?>
			<tr valign="top">
		        <th scope="row" valign="middle" align="left" ><?php echo esc_html($key) ?></th>
				<?php
				if($key == 'access_denied_text') {
					?>
					<td><textarea name="label_<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($default) ?>" style="width:100%; height:150px;"><?php echo esc_attr(Labels::getLabel($key)); ?></textarea></td>
					<?php
				} elseif ($key == 'access_denied_text_fade') {
					?>
					<td><textarea name="label_<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($default) ?>" style="width:100%; height:150px;"><?php echo esc_attr(Labels::getLabel($key)); ?></textarea></td>
					<?php
				} else {
					?>
					<td><input type="text" name="label_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr(Labels::getLabel($key)); ?>" placeholder="<?php echo esc_attr($default) ?>" style="width:100%; max-width:100%;" /></td>
					<?php
				}
				?>
				<td><?php echo Labels::getDescription($key); ?></td>
		    </tr>
	    <?php endif; ?>
	<?php endforeach; ?>
	</table>
<?php endforeach; ?>
<?php
echo $settingsView->renderSubcategory('labels', 'other');
?>