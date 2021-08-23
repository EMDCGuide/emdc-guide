<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\controller\FrontendController;

class DeleteButtonShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-delete-account';
	
	static function shortcode($atts, $deleteButtonText = null) {
		return FrontendController::getDeleteButton($deleteButtonText, $atts);
	}

}