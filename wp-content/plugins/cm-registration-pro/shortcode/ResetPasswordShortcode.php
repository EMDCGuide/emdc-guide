<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\controller\UserController;

class ResetPasswordShortcode extends Shortcode {

	const SHORTCODE_NAME = 'cmreg-reset-password';

	static function shortcode($atts, $text = '')
	{
		$atts = shortcode_atts(array(
			'showheader' => 0,
		), $atts);
		wp_enqueue_style('cmreg-frontend');
		wp_enqueue_script('cmreg-profile-edit');
		$nonce = wp_create_nonce(UserController::ACTION_EDIT);
		return UserController::loadFrontendView('reset-password', compact('atts', 'nonce'));
	}
}