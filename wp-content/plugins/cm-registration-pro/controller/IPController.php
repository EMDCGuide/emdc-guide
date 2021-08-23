<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\model\Labels;
use com\cminds\registration\App;
use com\cminds\registration\model\Settings;
use com\cminds\registration\helper\IPHelper;

class IPController extends Controller {
	
	static $actions = array(
		'register_post' => array('args' => 3),
	);

	static $filters = array(
		'authenticate' => array('args' => 3, 'priority' => 100),
	);
	
	static function authenticate($user, $username, $password) {
	
		if (!App::isLicenseOk()) return $user;
	
		$addError = function($errorCode, $msg) use (&$user) {
			if (is_wp_error($user)) {
				$user->add($errorCode, $msg);
			} else {
				$user = new \WP_Error($errorCode, $msg);
			}
		};
		
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $user;
		
		$ip = $_SERVER['REMOTE_ADDR'];
	
		// Allow only from specific IPs
		$allow = trim(Settings::getOption(Settings::OPTION_LOGIN_IP_ALLOW));
		if (!empty($allow)) {
			$allow = array_map('trim', explode("\n", $allow));
			if (!IPHelper::inList($ip, $allow)) {
				$addError('login_not_allowed_ip', Labels::getLocalized('login_error_not_allowed_ip'));
			}
		}

		// Deny specific IPs
		$deny = trim(Settings::getOption(Settings::OPTION_LOGIN_IP_DENY));
		if (!empty($deny)) {
			$deny = array_map('trim', explode("\n", $deny));
			if (IPHelper::inList($ip, $deny)) {
				$addError('login_not_allowed_ip', Labels::getLocalized('login_error_disallowed_ip'));
			}
		}
	
	
		return $user;
	}
	
	static function register_post($sanitized_user_login, $user_email, \WP_Error $errors) {
		if (App::isLicenseOk() AND $_SERVER['REQUEST_METHOD'] === 'POST') {
		
			// Allow only from specific IPs
			$allow = trim(Settings::getOption(Settings::OPTION_LOGIN_IP_ALLOW));
			if (!empty($allow)) {
				$allow = array_map('trim', explode("\n", $allow));
				if (!IPHelper::inList($ip, $allow)) {
					$errors->add('register_not_allowed_ip', Labels::getLocalized('register_not_allowed_ip'));
				}
			}
			
			// Deny specific IPs
			$deny = trim(Settings::getOption(Settings::OPTION_LOGIN_IP_DENY));
			if (!empty($deny)) {
				$deny = array_map('trim', explode("\n", $deny));
				if (IPHelper::inList($ip, $deny)) {
					$errors->add('register_disallowed_ip', Labels::getLocalized('register_error_disallowed_ip'));
				}
			}
			
		}
	}
	
}