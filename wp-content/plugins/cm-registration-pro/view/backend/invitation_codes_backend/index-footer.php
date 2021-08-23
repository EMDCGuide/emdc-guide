<?php
use com\cminds\registration\model\Settings;
?>
<div class="alignleft actions cmreg-download-csv">
	<a href="<?php echo esc_attr($downloadCSVUrl); ?>" class="button">
		Export All Codes
	</a>
</div>
<script>
jQuery(function($) {
	$(".cmreg-download-csv").insertBefore($(".tablenav.top .tablenav-pages"));
});
</script>

<div class="alignleft actions cmreg-delete-all-invitation-codes">
	<a href="<?php echo esc_attr($deleteAllInvitationCodes); ?>" class="button" onclick='return confirm("Are you sure want to delete all invitation codes?\nIt will permanently removed from your site!")'>
		Delete All Codes
	</a>
</div>
<script>
jQuery(function($) {
	$(".cmreg-delete-all-invitation-codes").insertBefore($(".tablenav.top .tablenav-pages"));
});
</script>

<div class="alignleft actions cmreg-download-invited-users-csv" style="width:100%; float:left; clear:both; margin:10px 0;">
	<div style="float:right;">
		<form method="GET" action="<?php echo esc_attr($downloadInvitedUsersCSV); ?>">
			<input type="hidden" name="cmreg_action" value="cmreg_download_invited_users_csv" />
			User Role
			<select name="cmreg_download_invited_users_role">
				<option value="">All</option>
				<?php
				$user_roles = Settings::getRolesOptions();
				if(count($user_roles) > 0) {
					foreach($user_roles as $rolekey=>$roleval) {
						?>
						<option value="<?php echo $rolekey; ?>"><?php echo $roleval; ?></option>
						<?php
					}
				}
				?>
			</select>
			Registered Date From
			<input type="date" name="cmreg_download_invited_users_dateto" value="<?php //echo date('Y-m-d'); ?>" />
			To
			<input type="date" name="cmreg_download_invited_users_datefrom" value="<?php //echo date('Y-m-d'); ?>" />
			<input type="submit" class="button" value="Export All Registered Users Codes" />
		</form>
	</div>
</div>
<script>
jQuery(function($) {
	$(".cmreg-download-invited-users-csv").insertBefore($(".tablenav.top"));
});
</script>