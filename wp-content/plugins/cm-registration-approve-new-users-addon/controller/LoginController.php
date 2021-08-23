<?php
namespace com\cminds\registration\addon\approvenewusers\controller;

use com\cminds\registration\addon\approvenewusers\controller\abstracts\ValidLicenseController;
use com\cminds\registration\addon\approvenewusers\model\User;
use com\cminds\registration\addon\approvenewusers\model\Labels;

class LoginController extends ValidLicenseController {
	
	static $filters = array(
		'authenticate' => array('args' => 3, 'priority' => 100),
	);
	
	static function authenticate($user, $username, $password) {
		$addError = function($errorCode, $msg) use (&$user) {
			if (is_wp_error($user)) {
				$user->add($errorCode, $msg);
			} else {
				$user = new \WP_Error($errorCode, $msg);
			}
		};
		if ($userData = get_user_by('login', $username) AND $userObj = User::getInstance($userData) AND $userObj->isApprovalNeeded()) {
			$addError('login_error_approval_needed', Labels::getLocalized('login_error_approval_needed'));
		}
		return $user;
	}
	
}