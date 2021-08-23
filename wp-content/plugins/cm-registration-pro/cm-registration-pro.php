<?php
/*
Plugin Name: CM Registration Pro
Plugin URI: https://www.cminds.com/store/cm-registration-and-invitation-codes-plugin-for-wordpress/
Description: Add AJAX-based login and registration forms with captcha, email verification, invitation codes and more.
Author: CreativeMindsSolutions
Version: 3.2.6
*/

if (version_compare('5.3', PHP_VERSION, '>')) {
	die(sprintf('We are sorry, but you need to have at least PHP 5.3 to run this plugin (currently installed version: %s)'
		. ' - please upgrade or contact your system administrator.', PHP_VERSION));
}

register_activation_hook(__FILE__, 'cmreg_install');
register_deactivation_hook(__FILE__, 'cmreg_uninstall');

function cmreg_install() {
	if (!get_option('cmreg_update_auto_ph')) {
		$cmreg_profile_field = get_posts(array('posts_per_page'=>-1,'post_type'=>'cmreg_profile_field'));
		if(count($cmreg_profile_field) > 0) {
			foreach($cmreg_profile_field as $field) {
				$key_1_value = get_post_meta($field->ID, 'cmreg_placeholder', true);
				if (empty($key_1_value)) {
					update_post_meta($field->ID, 'cmreg_placeholder', $field->post_title);
				}
			}
		}
		add_option('cmreg_update_auto_ph', 1);
	}
	return;
}

function cmreg_uninstall() {
	return;
}

require_once dirname(__FILE__) . '/App.php';
com\cminds\registration\App::bootstrap(__FILE__);