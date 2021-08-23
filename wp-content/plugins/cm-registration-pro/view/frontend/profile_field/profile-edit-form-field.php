<?php
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\ProfileField;
use com\cminds\registration\helper\FormBuilderRender;

$label = Labels::getLocalized($field->getLabel()).':';
if ($field->isRequired()) {
	$label .= ' ' . Labels::getLocalized('field_required');
}
?>
<div class="cmreg-registration-field">
	<?php if (Settings::getOption(Settings::OPTION_FORM_FIELD_LABEL_ENABLE)): ?>
		<label><?php echo $label; ?></label>
	<?php endif; ?>
	<?php echo FormBuilderRender::render('cmreg_extra_field', $field, $userId); ?>
</div>