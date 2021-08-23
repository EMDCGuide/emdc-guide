<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\model\Labels;
use com\cminds\registration\App;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\LoginAttempt;
use com\cminds\registration\helper\Recaptcha;

class LoginAttemptsController extends Controller {
	
	static $actions = array(
		'login_form' => array('args' => 1, 'priority' => 10000),
		//'register_post' => array('args' => 3),
	);

	static $filters = array(
		'authenticate' => array('args' => 3, 'priority' => PHP_INT_MAX),
		'cmreg_captcha_enabled',
		'cmreg_login_ajax_response',
	);
	
	static function login_form($place = null) {
		$action = Settings::getOption(Settings::OPTION_LOGIN_LIMIT_ATTEMPTS_ACTION);
		if ($action != Settings::LIMIT_ATTEMPTS_ACTION_DISABLED) {
			if (LoginAttempt::isLimitExceeded()) {
				//echo Labels::getLocalized('login_error_limit_attempts_exceeded');
			}
			switch ($action) {
				case Settings::LIMIT_ATTEMPTS_ACTION_SHOW_CAPTCHA:
					//CaptchaController::login_form($place);
					break;
				case Settings::LIMIT_ATTEMPTS_ACTION_WAIT:
					//echo Labels::getLocalized('login_error_limit_attempts_wait');
					break;
			}
		}
	}
	
	static function authenticate($user, $username, $password) {
		
		if(!is_admin()) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( is_plugin_active('cm-restrict-user-account-access/cm-restrict-user-account-access.php') ) {
			if(isset($user->ID)) {
				$dates = array(
					'activationDate' => get_user_meta( $user->ID, 'cmrua_meta_activation_date', true ),
					'restrictedDate' => get_user_meta( $user->ID, 'cmrua_meta_restrict_date', true )
				);
				$dates = apply_filters( 'cmrua_user_login_restriction_dates', $dates );
				if ($dates['activationDate'] != ''  && $dates['activationDate'] >= current_time( 'timestamp' ) ) { // login before activation date
					//User::sendRestrictionNotification( $user->ID );
					return new \WP_Error( 'broke', get_option('cmrua_option_login_error_message_not_activated', 'Sorry, your account is not activated.') );
				}
				if ($dates['restrictedDate'] != '' &&  $dates['restrictedDate'] <= current_time( 'timestamp' ) ) {
					//User::sendRestrictionNotification( $user->ID );
					return new \WP_Error( 'broke', get_option('cmrua_option_login_error_message', 'Sorry, you don\'t have the access anymore.') );
				}
				return $user;
			}
		}

		if (!App::isLicenseOk()) return $user;
		
		$action = Settings::getOption(Settings::OPTION_LOGIN_LIMIT_ATTEMPTS_ACTION);

		$recaptcha_api_site_key = Settings::getOption(Settings::OPTION_RECAPTCHA_API_SITE_KEY);
		$recaptcha_api_secret_key = Settings::getOption(Settings::OPTION_RECAPTCHA_API_SECRET_KEY);
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $action != Settings::LIMIT_ATTEMPTS_ACTION_DISABLED) {
			
			if (is_wp_error($user)) {
				
				// Log invalid login attempt
				LoginAttempt::create();
				
				// Show message about attempts left
				$count = LoginAttempt::getCurrentAttemptsNumber();
				$max = Settings::getOption(Settings::OPTION_LOGIN_LIMIT_ATTEMPTS_NUMBER);
				$left = $max - $count;
				if ($left > 0) {
					LoginController::authenticateAddError('cmreg_login_limit_attempts_msg', sprintf(Labels::getLocalized('login_limit_attempts_msg'), $left), $user);
				}
				
			} else {
				// Login is valid
				
			}
			
			if (LoginAttempt::isLimitExceeded()) {
				// Limit has been exceeded
				if ($action == Settings::LIMIT_ATTEMPTS_ACTION_WAIT) {
					LoginController::authenticateAddError('cmreg_login_error_limit_attempts_exceeded', Labels::getLocalized('login_error_limit_attempts_exceeded'), $user);
					
					$waitMinutes = LoginAttempt::whenTryAgainMinutes();
					LoginController::authenticateAddError('cmreg_login_error_limit_attempts_wait',
							sprintf(Labels::getLocalized('login_error_limit_attempts_wait'), $waitMinutes), $user);
				}
				else if ($action == Settings::LIMIT_ATTEMPTS_ACTION_SHOW_CAPTCHA && $recaptcha_api_site_key != '' && $recaptcha_api_secret_key !='' ) {
					$code = filter_input(INPUT_POST, Recaptcha::POST_RESPONSE);
					if (empty($code)) {
						// Show message that user now have to enter the captcha
						LoginController::authenticateAddError('cmreg_login_error_limit_attempts_captcha', Labels::getLocalized('login_error_limit_attempts_captcha'), $user);
					}
				}
			}
			
		}
		
		return $user;
		
	}
	
	static function cmreg_captcha_enabled($enabled) {
		$action = Settings::getOption(Settings::OPTION_LOGIN_LIMIT_ATTEMPTS_ACTION);
		if ($action == Settings::LIMIT_ATTEMPTS_ACTION_SHOW_CAPTCHA AND LoginAttempt::isLimitExceeded()) {
			$enabled = true;
		}
		return $enabled;
	}
	
	static function cmreg_login_ajax_response($response) {
		$action = Settings::getOption(Settings::OPTION_LOGIN_LIMIT_ATTEMPTS_ACTION);
		if ($action == Settings::LIMIT_ATTEMPTS_ACTION_SHOW_CAPTCHA AND LoginAttempt::isLimitExceeded()) {
			$response['showCaptcha'] = CaptchaController::getCaptchaBlock();
		}
		return $response;
	}

}