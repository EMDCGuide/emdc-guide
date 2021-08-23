<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\controller\InvitationCodesController;
use com\cminds\registration\model\InvitationCode;
use com\cminds\registration\model\Settings;

class ListUsersInvitationsShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-list-users-invitations';
	
	static function shortcode($atts, $text = '') {
		
		$atts = shortcode_atts(array(
			'used' => 'all'
		), $atts);
		
		if (is_user_logged_in()) {
			wp_enqueue_style('cmreg-frontend');
			$codes = InvitationCode::getByAuthor(get_current_user_id());
			$defaultRole = Settings::getOption(Settings::OPTION_REGISTER_DEFAULT_ROLE);
			return InvitationCodesController::loadFrontendView('users-invitations-list', compact('atts', 'codes', 'defaultRole'));
		}
	}

}