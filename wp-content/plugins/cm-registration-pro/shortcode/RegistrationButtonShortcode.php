<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\model\Settings;
use com\cminds\registration\model\Labels;
use com\cminds\registration\controller\FrontendController;

class RegistrationButtonShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-registration-btn';
	
	static function shortcode($atts, $buttonText = null) {
		if (empty($buttonText)) {
			$buttonText = 'Registration';
		}
		if (!is_user_logged_in()) {
			if(isset($atts['href'])) {
				$atts['href'] = '#cmreg-only-registration-click';
			} else {
				if(!is_array($atts)) {
					$atts = array();
					array_merge($atts, array('href', '#cmreg-only-registration-click'));
				} else {
					array_merge($atts, array('href', '#cmreg-only-registration-click'));
				}
			}
			return FrontendController::getLoginButton($buttonText, $atts);
		}
	}

}