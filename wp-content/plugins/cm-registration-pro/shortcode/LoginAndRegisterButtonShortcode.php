<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\model\Settings;
use com\cminds\registration\model\Labels;
use com\cminds\registration\controller\FrontendController;

class LoginAndRegisterButtonShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-login';

	static function shortcode($atts, $loginButtonText = null) {

		$atts = shortcode_atts(array(
			'redirect-to' => '',
			'after-login' => '0',
			'after-text' => '',
		), $atts);

		return FrontendController::getLoginButton($loginButtonText, $atts);
	}

}