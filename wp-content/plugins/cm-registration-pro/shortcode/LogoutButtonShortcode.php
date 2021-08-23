<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\controller\FrontendController;

class LogoutButtonShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-logout-btn';
	
	static function shortcode($atts, $logoutButtonText = null) {
		return FrontendController::getLogoutButton($logoutButtonText, $atts);
	}

}