<?php
namespace com\cminds\siteaccessrestriction\shortcode;

use com\cminds\siteaccessrestriction\model\Settings;
use com\cminds\siteaccessrestriction\model\User;

class ContentShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'access';
	
	static function shortcode($atts, $content = null) {
		
		if (in_array('deniedtext_html', $atts)) {
			$deniedtext_html_exist = 1;
		} else {
			$deniedtext_html_exist = 0;
		}

		$atts = shortcode_atts(array(
			'role' => '',
			'cap' => '',
			'userid' => '',
			'guests' => '',
			'login' => '',
			'deniedtext' => '',
			'reverse' => 0,
			'doshortcode' => 1,
            'blacklist' => '',
            'whitelist' => '',
		), $atts);

		$atts['blacklist'] = (!empty($atts['blacklist'])) ? explode(',', $atts['blacklist']) : array();
		$atts['whitelist'] = (!empty($atts['whitelist'])) ? explode(',', $atts['whitelist']) : array();

		$allow = true;
		
		if (!empty($atts['role']) AND !User::hasRole($atts['role'])) {
			$allow = false;
		}
		
		if (!empty($atts['cap']) AND !User::hasCapability($atts['cap'])) {
			$allow = false;
		}

		if (!empty($atts['userid'])) {
			$userids = explode(",", $atts['userid']);
			if (!in_array(get_current_user_id(), $userids)) {
				$allow = false;
			}
		}
		
		if (isset($atts['guests']) AND $atts['guests'] == '1' AND is_user_logged_in()) {
			$allow = false;
		}
		
		if (isset($atts['login']) AND $atts['login'] == '1' AND !is_user_logged_in()) {
			$allow = false;
		}

		if (!empty($atts['blacklist']) AND ((is_user_logged_in() AND in_array(get_currentuserinfo()->nickname, $atts['blacklist'])) OR !is_user_logged_in())){
            $allow = false;
        }

		if (!empty($atts['whitelist']) AND ((is_user_logged_in() AND !in_array(get_currentuserinfo()->nickname, $atts['whitelist'])) OR !is_user_logged_in())){
            $allow = false;
        }

		if ($atts['reverse']) {
			$allow = !$allow;
		}

		if (!$allow) {

			if ($deniedtext_html_exist) {
				$result = get_option(Settings::OPTION_SHORTCODE_ACCESS_DENIED_TEXT, 'Access Denied');

			} elseif (isset($atts['deniedtext'])) {

				$result = $atts['deniedtext'];
			}

		} else {
			$result = $content;
		}

		return ($atts['doshortcode'] ? do_shortcode($result) : $result);
		
	}
	
}