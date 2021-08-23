<?php
/*
Plugin Name: CM Site Access Restriction Pro
Plugin URI: https://www.cminds.com/wordpress-plugins-library/membership-plugin-for-wordpress
Description: A gated content powerful solution and restricted content plugin for WordPress. Support restricted content access by role on your WP site.
Author: CreativeMindsSolutions
Version: 1.5.9
*/

if (version_compare('5.3', PHP_VERSION, '>')) {
	die(sprintf('We are sorry, but you need to have at least PHP 5.3 to run this plugin (currently installed version: %s)'
		. ' - please upgrade or contact your system administrator.', PHP_VERSION));
}

require_once dirname(__FILE__) . '/App.php';
com\cminds\siteaccessrestriction\App::bootstrap(__FILE__);