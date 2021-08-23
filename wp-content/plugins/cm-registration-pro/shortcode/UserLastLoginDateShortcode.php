<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\model\Settings;
use com\cminds\registration\model\Labels;
// use com\cminds\registration\controller\SocialLoginController;

class UserLastLoginDateShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg_login_date';

	static function shortcode($atts, $id = '') {

		$atts = shortcode_atts(array(
			'id' => '',
		), $atts);

		
      if ($date = get_user_meta( $atts['id'], 'last_login', true )) {
		 return date_i18n( get_option('date_format'), $date) . ' ' . date_i18n( get_option('time_format'), $date);
      } else {
         return;
      }
	}
	
}