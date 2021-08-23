<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\controller\UserController;
use com\cminds\registration\controller\ProfileFieldController;

class EditProfileShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-edit-profile';
	
	static function shortcode($atts, $text = '') {
		
		$atts = shortcode_atts(array(
			'showheader' => 1,
		), $atts);
		
		if (is_user_logged_in()) {
			
			$userId = get_current_user_id();
			
			wp_enqueue_style('cmreg-frontend');
			wp_enqueue_script('cmreg-profile-edit');
			
			/*
 			$fields = ProfileField::getAll();
 			$extraFields = Settings::getOption(Settings::OPTION_REGISTER_EXTRA_FIELDS);
 			if (is_array($extraFields)) {
 				array_shift($extraFields);
 				foreach ($extraFields as $i => &$field) {
 					$field['value'] = User::getExtraField($userId, $field['meta_name']);
 				}
 			}
			*/
			
			$nonce = wp_create_nonce(UserController::ACTION_EDIT);
			
			return UserController::loadFrontendView('profile-edit-form', compact('atts', 'nonce'));
		}
	}

}