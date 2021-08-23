<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\controller\InvitationCodesController;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\User;

class CreateInvitationCodeShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-create-invitation-code';
	const TRANSIENT_ATTS_PREFIX = 'cmreg_atts_';
	
	static function shortcode($atts, $text = '') {
		
		$atts = shortcode_atts(array(
			'expiration' => '',
			'userslimit' => 0,
			'verifyemail' => 'global',
			'emailinput' => 1,
			'role' => '',
			'showparams' => 1,
			'showlink' => 0,
		), $atts);
		
		if (is_user_logged_in()) {
			
			$userId = get_current_user_id();
			
			wp_enqueue_style('cmreg-frontend');
			wp_enqueue_script('cmreg-create-invitation-code');
			
			if (!User::canInviteUsers($userId)) {
				return InvitationCodesController::loadFrontendView('create-invitation-code-limit-reached');
			}
			
			$nonce = wp_create_nonce(InvitationCodesController::ACTION_USER_CREATE_CODE);
			
			$verifyEmail = $atts['verifyemail'];
			if ($verifyEmail == 'global') {
				$verifyEmail = Settings::getOption(Settings::OPTION_REGISTER_EMAIL_VERIFICATION_ENABLE);
			}
			
			$userRole = $atts['role'];
			if (empty($userRole)) {
				$userRole = Settings::getOption(Settings::OPTION_REGISTER_DEFAULT_ROLE);
			}
			$userRoleName = User::getRoleNameByKey($userRole);
			
			if (!empty($atts['expiration'])) {
				if ($time = strtotime($atts['expiration'])) {
					$atts['expiration'] = Date('Y-m-d H:i:s', $time);
				}
			}
			
			$url = admin_url('admin-ajax.php');
			$action = InvitationCodesController::ACTION_USER_CREATE_CODE;
			$nonce = wp_create_nonce(InvitationCodesController::ACTION_USER_CREATE_CODE);
			$attsHash = static::getAttributesTransientHash($atts);
			
			return InvitationCodesController::loadFrontendView('create-invitation-code',
					compact('atts', 'nonce', 'verifyEmail', 'userRole', 'userRoleName', 'url', 'action', 'nonce', 'attsHash'));
			
		}
	}
	
	protected static function getAttributesTransientHash($atts) {
		$hash = md5(serialize($atts));
		$transient = static::TRANSIENT_ATTS_PREFIX . $hash;
		set_transient($transient, $atts);
		return $hash;
	}
	
	static function getAttributesByHash($hash) {
		$transient = static::TRANSIENT_ATTS_PREFIX . $hash;
		return get_transient($transient);
	}
	
}