<?php
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\ProfileField;
use com\cminds\registration\helper\FormBuilderRender;

$hide_password_characters = Settings::getOption(Settings::OPTION_PASSWORD_HIDE_CHARS);

$label = Labels::getLocalized($field->getLabel());
if ($field->isRequired()) {
	$label .= ' ' . Labels::getLocalized('field_required');
}
$containter_class = '';
$containter_class = $field->getUserMetaKey();
if($containter_class != '') {
	$containter_class = ' '.$containter_class.'_rowcontainer';
}
$field_class = '';
$field_class = $field->getCSSClass();
if($field_class != '') {
	$field_class = str_replace('form-control','',$field_class);
	$field_class = ' '.trim($field_class);
}

if(isset($_GET['action']) && $_GET['action'] == 'register') {
	if($field->getUserMetaKey() != 'username' && $field->getUserMetaKey() != 'email') {
		?>
		<div class="cmreg-registration-field<?php echo $containter_class; ?><?php echo $field_class; ?>">
			<?php if (Settings::getOption(Settings::OPTION_FORM_FIELD_LABEL_ENABLE)): ?>
				<label class="cmreg-label"><?php echo $label; ?></label>
			<?php endif; ?>
			<?php echo FormBuilderRender::render('cmreg_extra_field', $field); ?>
		</div>
		<?php
	}
} else {
	if($field->getRegistrationFormRole() == 'cmregpw') {
		?>
		<div class="cmreg-registration-field cmreg-password-block<?php echo $containter_class; ?><?php echo $field_class; ?><?php echo $hide_password_characters == 1 ? ' show_as_password' : ''; ?>">
			<?php if (Settings::getOption(Settings::OPTION_FORM_FIELD_LABEL_ENABLE)): ?>
				<label class="cmreg-label"><?php echo $label; ?></label>
			<?php endif; ?>
			<input id="cmreg_<?php echo $field->getUserMetaKey(); ?>" type="<?php echo $hide_password_characters == 1 ? 'password' : 'text'; ?>" name="cmreg_extra_field[<?php echo $field->getUserMetaKey(); ?>]" class="cmreg_accesscode_value" placeholder="<?php echo Labels::getLocalized($field->getPlaceholder()); ?>" />
			<?php if($hide_password_characters == 1) { ?>
				<a href="javascript:void(0);" class="cmreg-input-type-trigger"><span class="dashicons dashicons-hidden"></span></a>
			<?php } ?>
		</div>
		<?php
	} else if ($field->getRegistrationFormRole() == 'cmregpwrepeat') {
		?>
		<div class="cmreg-registration-field cmreg-password-block-re<?php echo $containter_class; ?><?php echo $field_class; ?><?php echo $hide_password_characters == 1 ? ' show_as_password' : ''; ?>">
			<?php if (Settings::getOption(Settings::OPTION_FORM_FIELD_LABEL_ENABLE)): ?>
				<label class="cmreg-label"><?php echo $label; ?></label>
			<?php endif; ?>
			<input id="cmreg_<?php echo $field->getUserMetaKey(); ?>" type="<?php echo $hide_password_characters == 1 ? 'password' : 'text'; ?>" name="cmreg_extra_field[<?php echo $field->getUserMetaKey(); ?>]" class="cmreg_accesscode_value" placeholder="<?php echo Labels::getLocalized($field->getPlaceholder()); ?>" />
			<?php if($hide_password_characters == 1) { ?>
				<a href="javascript:void(0);" class="cmreg-input-type-trigger-re"><span class="dashicons dashicons-hidden"></span></a>
			<?php } ?>
		</div>
		<?php
	} else {
		?>
		<div class="cmreg-registration-field<?php echo $containter_class; ?><?php echo $field_class; ?>">
			<?php if (Settings::getOption(Settings::OPTION_FORM_FIELD_LABEL_ENABLE)): ?>
				<label class="cmreg-label"><?php echo $label; ?></label>
			<?php endif; ?>
			<?php echo FormBuilderRender::render('cmreg_extra_field', $field); ?>
		</div>
		<?php
	}
}
?>