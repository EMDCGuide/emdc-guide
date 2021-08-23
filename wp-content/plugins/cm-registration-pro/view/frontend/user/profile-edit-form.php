<?php
use com\cminds\registration\model\User;
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;

$writeTextField = function($label, $name, $value, $type = 'text', $required = false, $maxlen = null) {
	$required = ($required ? ' required' : '');
	$maxlen = ($maxlen ? ' maxlength="'. $maxlen .'"' : '');
	printf('<p class="cmreg-field-%s"><label>%s</label><input type="%s" name="%s" value="%s"%s /></p>',
		esc_attr($name), $label, esc_attr($type), esc_attr($name), esc_attr($value), $required . $maxlen);
};
$writeTextareaField = function($label, $name, $value, $type = 'text') {
	printf('<p class="cmreg-field-%s"><label>%s</label><textarea name="%s">%s</textarea></p>', esc_attr($name), $label, esc_attr($name), esc_html($value));
};
if (empty($userId)) $userId = get_current_user_id();
$user = User::getUserData($userId);
$isadmin = 'no';
if(is_admin()) {
	$isadmin = 'yes';
}
?>
<form action="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>" method="post" class="cmreg-form cmreg-profile-edit-form">
	
	<?php if (!empty($atts['showheader'])): ?>
		<p class="profile_edit_form_heading"><?php echo Labels::getLocalized('user_profile_edit_form_header'); ?></p>
	<?php endif; ?>

	<?php if (!Settings::getOption(Settings::OPTION_HIDE_DISPLAY_NAME_FIELD)): ?>
		<?php $writeTextField(Labels::getLocalized('user_profile_display_name'), 'display_name', $user->display_name, 'text', $required = true, $maxlength = 255); ?>
	<?php endif; ?>
	
	<?php
	$writeTextField(Labels::getLocalized('user_profile_email'), 'email', $user->user_email, 'email', $required = true, $maxlength = 255);
	if (!is_admin() && Settings::getOption(Settings::OPTION_EMAIL_CONFIRM_ENABLE)) {
		echo "<p class='cmreg-field-email-description'>".Labels::getLocalized('user_profile_email_description')."</p>";
		if($user->cmreg_email_comfirm) {
			$confirm = Labels::getLocalized('user_profile_email_cancel_confirm');
			echo "<p class='cmreg-field-email-confirm' data-key='".$user->cmreg_email_comfirm_key."'>".sprintf(Labels::getLocalized('user_profile_email_change'), $user->cmreg_email_comfirm).' <a href="'.add_query_arg('cmreg-cancel-email', 'yes').'" onclick="return confirm(\''.$confirm.'\')">'.Labels::getLocalized('user_profile_email_cancel')."</a></p>";
		}
	}
	?>
	
	<?php if (!Settings::getOption(Settings::OPTION_HIDE_WEBSITE_URL_FIELD)): ?>
		<?php $writeTextField(Labels::getLocalized('user_profile_website'), 'website', $user->user_url); ?>
	<?php endif; ?>
	
	<?php if (!Settings::getOption(Settings::OPTION_HIDE_ABOUT_ME_FIELD)): ?>
		<?php $writeTextareaField(Labels::getLocalized('user_profile_description'), 'description', $user->description); ?>
	<?php endif; ?>
	
	<?php do_action('cmreg_profile_edit_form', $user->ID); ?>

	<div class="form-summary">
		<input type="hidden" name="action" value="cmreg_user_profile_edit" />
		<input type="hidden" name="isadmin" value="<?php echo $isadmin; ?>" />
		<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
		<?php if (is_admin() AND isset($userId)): ?>
			<input type="hidden" name="userId" value="<?php echo esc_attr($userId); ?>">
		<?php endif; ?>
		<a href="/navigator/" class="button button-primary return_btn"><?php echo esc_attr(Labels::getLocalized('user_profile_return_btn')); ?></a>
		<a href="<?php echo wp_logout_url(home_url()); ?>" class="button button-primary logout_btn"><?php echo esc_attr(Labels::getLocalized('user_profile_logout_btn')); ?></a>
		<input type="submit" class="button button-primary save_btn" value="<?php echo esc_attr(Labels::getLocalized('user_profile_save_btn')); ?>" />
	</div>

</form>