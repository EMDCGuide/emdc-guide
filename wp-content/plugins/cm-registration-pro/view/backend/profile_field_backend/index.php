<?php
use com\cminds\registration\controller\ProfileFieldBackendController;
?>
<div id="cmreg-form-wrap"></div>
<form method="post" id="cmreg-profile-fields-form" autocomplete="off">
	<textarea name="data"></textarea>
	<div class="cmreg-buttons">
		<input type="hidden" name="nonce" value="<?php echo $nonce; ?>">
		<input type="submit" value="Save" class="button button-primary cmreg-profile-fields-save-btn">
	</div>
	<div class="cmreg-profile-fields-download-csv">
		<h2>Import data into CSV</h2>
		<p>You can download a CSV file with the users 'profile fields' values using the following button.</p>
		<a href="<?php echo esc_attr(ProfileFieldBackendController::getExportCSVUrl()); ?>" class="button">Download CSV</a>
	</div>
</form>
<script type="text/javascript">
jQuery(function($) {
	CMREG_Profile_Fields.initialize('#cmreg-form-wrap', <?php echo json_encode(json_encode($fieldsData)); ?>, <?php echo json_encode($roles); ?>);
});
</script>