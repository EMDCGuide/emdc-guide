<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\controller\InvitationCodesController;
use com\cminds\registration\model\InvitationCode;
use com\cminds\registration\model\Settings;

class ListChildUsersInvitationsShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-my-users-from-codes';
	
	static function shortcode($atts, $text = '') {
		
		global $wpdb;

		$atts = shortcode_atts(array(
			
		), $atts);
		
		if (is_user_logged_in()) {
			wp_enqueue_style('cmreg-frontend');

			$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."usermeta where meta_key='cmreg_invitation_code_author_id' and meta_value='".get_current_user_id()."'" );
			$myusers = array();
			if(count($results) > 0) {
				foreach($results as $user) {
					$user_id = $user->user_id;
					$user_info = get_userdata($user_id);
					$myusers[] = $user_info;
				}
			}
			return InvitationCodesController::loadFrontendView('child-users-invitations-list', compact('atts', 'myusers'));
		}
	}

}