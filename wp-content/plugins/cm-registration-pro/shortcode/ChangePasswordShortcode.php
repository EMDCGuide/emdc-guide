<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\controller\UserController;

class ChangePasswordShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-change-password';
	
	static function shortcode($atts, $text = '') {
		
		$atts = shortcode_atts(array(
			'showheader' => 0,
		), $atts);
		
		if (is_user_logged_in()) {
			
			$userId = get_current_user_id();
			
			wp_enqueue_style('cmreg-frontend');
			wp_enqueue_script('cmreg-profile-edit');
			
			$nonce = wp_create_nonce(UserController::ACTION_EDIT);

			return UserController::loadFrontendView('change-password', compact('atts', 'nonce'));
		}
	}
}