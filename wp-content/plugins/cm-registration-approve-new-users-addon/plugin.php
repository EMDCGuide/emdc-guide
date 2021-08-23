<?php
/*
Plugin Name: CM Registration Approve New Users Addon
Description: Support moderating user's registration and approving each user manually. Can only be used with CM Registration and Invitation Codes Pro
Author: CreativeMindsSolutions
Version: 1.1.2
*/

if (version_compare('5.3', PHP_VERSION, '>')) {
	die(sprintf('We are sorry, but you need to have at least PHP 5.3 to run this plugin (currently installed version: %s)'
		. ' - please upgrade or contact your system administrator.', PHP_VERSION));
}

require_once dirname(__FILE__) . '/App.php';
com\cminds\registration\addon\approvenewusers\App::bootstrap(__FILE__);