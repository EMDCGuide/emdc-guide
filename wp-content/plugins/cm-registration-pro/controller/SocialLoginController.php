<?php
namespace com\cminds\registration\controller;

use com\cminds\downloadmanager\addon\clientdownloadzone\model\Label;
use com\cminds\registration\model\User;
use com\cminds\registration\model\Labels;
use com\cminds\registration\App;
use com\cminds\registration\model\Settings;

class SocialLoginController extends Controller {

	const URL_PART_SOCIAL_LOGIN = 'cminds-registration-social-login';
	const PARAM_SOCIAL_LOGIN_ERROR = 'cmreg_social_login_error';
	
	const TRANSIENT_OAUTH_RESPONSE_PREFIX = 'cmregoauth_';
	
	static $actions = array(
		//'template_redirect' => array('priority' => PHP_INT_MAX),
		'wp_enqueue_scripts',
		'init' => array('priority' => 11),
		'cmreg_login_form_bottom' => array('args' => 1),
		'cmreg_register_form_bottom' => array('args' => 1),
		'wp_footer',
	);
	
	static $ajax = array(
		'cmreg_social_login_invitation_code',
	);
	
	static function wp_enqueue_scripts() {
		if ($error = filter_input(INPUT_GET, static::PARAM_SOCIAL_LOGIN_ERROR)) {
			
			$msg = htmlspecialchars(strip_tags($error));
			wp_enqueue_script('cmreg_show_toast_message');
			wp_localize_script('cmreg_show_toast_message', 'cmreg_show_toast_message', compact('msg'));
			
			/*
 			$label = Labels::getLocalized($error);
 			if (strpos($error, 'cmreg_social_login_error_') !== 0 OR $label == $error) {
 				$label = Labels::getLocalized('cmreg_social_login_error_generic');
 			}
			
 			static::displayErrorPage($label);
			*/
		}
	}
	
	static protected function displayErrorPage($content) {
		$url = add_query_arg(static::PARAM_SOCIAL_LOGIN_ERROR, urlencode($content), site_url('/'));
		static::redirect($url);
		//echo static::loadFrontendView('error-template', compact('content'));
		//exit;
	}

	static function cmreg_login_form_bottom($atts) {
		if (!App::isLicenseOk()) return;
		if (!empty($atts['social-login'])) {
			echo self::getButtonsView(Labels::getLocalized('social_login_btn_prefix'));
		}
	}
	
	static function cmreg_register_form_bottom($atts) {
		if (!App::isLicenseOk()) return;
		if (!empty($atts['social-login'])) {
			echo self::getButtonsView(Labels::getLocalized('social_login_register_btn_prefix'));
		}
	}
	
	static function getButtonsView($text = '') {
		
		if (!Settings::getOption(Settings::OPTION_SOCIAL_LOGIN_ENABLE)) return;
		if (is_user_logged_in()) return;
		
		if (empty($text)) $text = Labels::getLocalized('social_login_btn_prefix');
		
		$out = '';
		if ($appId = Settings::getOption(Settings::OPTION_SOCIAL_LOGIN_FACEBOOK_APP_ID)) {
			$url = static::getFacebookAuthUrl();
			$out .= static::loadFrontendView('login-facebook', compact('appId', 'url', 'text'));
		}
		
		if ($appId = Settings::getOption(Settings::OPTION_SOCIAL_LOGIN_GOOGLE_APP_ID)) {
			$url = static::getGoogleAuthUrl();
			$out .= static::loadFrontendView('login-google', compact('appId', 'url', 'text'));
		}
		
		return $out;
		
	}
	
	static function getFacebookAuthUrl() {
		return site_url('/' . static::URL_PART_SOCIAL_LOGIN . '/facebook/');
	}
	
	static function getFacebookValidCallbackUrl() {
		return static::getFacebookAuthUrl() . 'int_callback';
	}
	
	static function getGoogleAuthUrl() {
		return site_url('/' . static::URL_PART_SOCIAL_LOGIN . '/google/');
	}
	
	static function getGoogleValidCallbackUrl() {
		return static::getGoogleAuthUrl() . 'oauth2callback';
	}
	
	static function init() {
		
		//if ($_GET['cmtest']) {
 		//	static::displayInvitationCodeForm(array());
 		//}
		
		if (!App::isLicenseOk()) return;
		if (!Settings::getOption(Settings::OPTION_SOCIAL_LOGIN_ENABLE)) return;
		
		/*
		 * URLs calling order:
		 * 
		 * http://local.cminds.review/cminds-registration-social-login/facebook
		 * http://local.cminds.review/cminds-registration-social-login/facebook/int_callback
		 * http://local.cminds.review/cminds-registration-social-login/callback
		 * 
		 */
		
		$url = static::getCurrentUrl();
		
		if (strpos($url, static::URL_PART_SOCIAL_LOGIN . '/callback') !== false) {
			// Process callback
			$Opauth = static::initOpauth($run = false);
			if ($response = static::getResponse($Opauth) AND isset($response['auth'])) {
				static::processSocialLoginData($response);
			}
		}
		else if (strpos($url, static::URL_PART_SOCIAL_LOGIN) !== false) {
			//static::displayErrorPage('Sorry, couldnt register a user.');
			// Initialize Opauth social login
			static::initOpauth($run = true);
		}
	}
	
	static protected function getCurrentUrl() {
		$url = filter_input(INPUT_SERVER, 'REQUEST_URI');
		if (empty($url) AND isset($_SERVER['REQUEST_URI'])) {
			$url = $_SERVER['REQUEST_URI'];
		}
		return $url;
	}
	
	static protected function initOpauth($run) {
		$config = array(
			//'path' => '/'. static::URL_PART_SOCIAL_LOGIN .'/',
			'path' => trailingslashit(parse_url(site_url('/'. static::URL_PART_SOCIAL_LOGIN ), PHP_URL_PATH)),
			'callback_url' => '{path}callback',
			'security_salt' => 'nasdfajsdfjkhawer0o24i35rjkhnsfgvnskasdfjklkv',
			'Strategy' => array(
				'Facebook' => array(
					'app_id' => Settings::getOption(Settings::OPTION_SOCIAL_LOGIN_FACEBOOK_APP_ID),
					'app_secret' => Settings::getOption(Settings::OPTION_SOCIAL_LOGIN_FACEBOOK_APP_SECRET),
					'scope' => 'email',
				),
				'Google' => array(
					'client_id' => Settings::getOption(Settings::OPTION_SOCIAL_LOGIN_GOOGLE_APP_ID),
					'client_secret' => Settings::getOption(Settings::OPTION_SOCIAL_LOGIN_GOOGLE_APP_SECRET),
					'scope' => 'email',
				),
			),
		);
			
		if (!class_exists('Opauth')) {
			require_once App::path('lib/Opauth/Opauth.php');
		}
		return new \Opauth( $config, $run );
	}
	
	static protected function getResponse($Opauth) {
		
		/**
		 * Fetch auth response, based on transport configuration for callback
		 */
		$response = null;
		
		switch($Opauth->env['callback_transport']){
			case 'session':
				if (!session_id()) session_start();
				if (isset($_SESSION['opauth'])) {
					$response = $_SESSION['opauth'];
					unset($_SESSION['opauth']);
				}
				break;
			case 'post':
				if (isset($_POST['opauth'])) {
					//$response = unserialize(base64_decode( $_POST['opauth'] ));
					$response = json_decode(base64_decode( $_POST['opauth'] ), true);
				}
				break;
			case 'get':
				if (isset($_GET['opauth'])) {
					//$response = unserialize(base64_decode( $_GET['opauth'] ));
					$response = json_decode(base64_decode( $_GET['opauth'] ), true);
				}
				break;
			default:
				static::displayErrorPage('CM Registration Opauth - Unsupported callback_transport');
				break;
		}
		
		if (empty($response)) {
			static::displayErrorPage('CM Registration Opauth - Authentication error: Opauth returns empty auth response');
		}
		
		/**
		 * Check if it's an error callback
		 */
		else if (array_key_exists('error', $response)) {
			//var_dump($response['error']);exit;
			static::displayErrorPage('CM Registration Opauth - Authentication error: Opauth returns error auth response. ' . json_encode($response['error']['message']));
		}
		
		/**
		 * Auth response validation
		 *
		 * To validate that the auth response received is unaltered, especially auth response that
		 * is sent through GET or POST.
		 */
		else{
			if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid'])){
				static::displayErrorPage('CM Registration Opauth - Invalid auth response: Missing key auth response components');
			}
			elseif (!$Opauth->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason)){
				static::displayErrorPage('CM Registration Opauth - Invalid auth response: ' . $reason);
			}
			else{
		
				/**
				 * It's all good. Go ahead with your application-specific authentication logic
				 */
			}
		}
		
		return $response;
		
	}

	static function processSocialLoginData($data) {
		
		// Disable captcha
		add_filter(CaptchaController::FILTER_CATPCHA_ENABLED, '__return_false');
		
		$provider = $data['auth']['provider'];
		$displayName = $data['auth']['info']['name'];
		$uid = $data['auth']['uid'];
		$email = (isset($data['auth']['info']['email']) ? $data['auth']['info']['email'] : null);
				
		// Find user with the same uid
		$userId = User::getBySocialLoginUID($provider, $uid);
		if (empty($userId) AND !empty($email)) {
			$userId = User::getByEmail($email);
		}
		
		if (!empty($userId)) {
			
			static::loginAndRedirect($userId);
			
		} else {
			
			if (!empty($email) AND Settings::getOption(Settings::OPTION_SOCIAL_LOGIN_ENABLE_ALLOW_REGISTRATION)) {
				
				if (Settings::getOption(Settings::OPTION_SOCIAL_LOGIN_ASK_INVITATION_CODE)) {
					static::displayInvitationCodeForm($data);
				} else {
					static::registerAndRedirect($email, $uid, $provider, $displayName);
				}
				
			} else {
				static::displayErrorPage(Labels::getLocalized('cmreg_social_login_error_unknown_user'));
				//static::redirect(site_url('/?cmreg_social_login_msg=unknown_user'));
				//exit;
			}
			
		}
		
		static::displayErrorPage(Labels::getLocalized('cmreg_social_login_error_generic') . '...');
		//die('cmreg social login: this shouldn\'t happen');
		//exit;
		
	}

	static function displayInvitationCodeForm($oauthResponse) {
		
		// Temporarily store the oAuth response
		$cacheKey = sha1(mt_rand() . time() . serialize($oauthResponse));
		$transient = static::TRANSIENT_OAUTH_RESPONSE_PREFIX . $cacheKey;
		set_transient($transient, $oauthResponse, $expiration = 3600*24);
		
		wp_enqueue_style('cmreg-frontend');
		wp_enqueue_script('cmreg-social-login-invitation-code');
		
		$actionUrl = admin_url('admin-ajax.php');
		$nonce = wp_create_nonce(static::TRANSIENT_OAUTH_RESPONSE_PREFIX);
		$invitationCodeRequired = (Settings::getOption(Settings::OPTION_REGISTER_INVIT_CODE) === Settings::INVITATION_CODE_REQUIRED);
		$content = static::loadFrontendView('invitation-code-form', compact('actionUrl', 'nonce', 'cacheKey', 'invitationCodeRequired'));
		echo static::loadFrontendView('blank-template', compact('content'));
		exit;
		
	}
	
	static function registerAndRedirect($email, $uid, $provider, $displayName) {
		try {
			$userId = static::registerUser($email, $uid, $provider, $displayName);
			static::loginAndRedirect($userId);
		} catch (\Exception $e) {
			static::displayErrorPage(Labels::getLocalized('cmreg_social_login_error_registration') .' ' . $e->getMessage());
			// static::redirect(site_url('/?cmreg_social_login_msg=registration_error&msg=' . urlencode($e->getMessage())));
			// exit;
		}
	}
	
	/**
	 * Register user
	 * 
	 * @throws Exception
	 * @param unknown $email
	 * @param unknown $uid
	 * @param unknown $provider
	 * @param unknown $displayName
	 */
	static protected function registerUser($email, $uid, $provider, $displayName) {
		
		$login = $email;
		$password = sha1(microtime() . $uid . $provider . mt_rand()) . 'Az.123';
		
		// Disable some verifications
		add_filter(CaptchaController::FILTER_CATPCHA_ENABLED, '__return_false');
		add_filter(ProfileFieldController::FILTER_PROCESSING_ENABLED, '__return_false');
		add_filter(EmailVerificationController::FILTER_VERIFICATION_ENABLED, '__return_false');
		
		$userId = User::register($email, $password, $login, $displayName);
		User::setSocialLoginUID($userId, $provider, $uid);
		
		return $userId;
		
	}

	static protected function loginAndRedirect($userId) {
		$redirect = site_url('/');
		try {
			$redirect = static::loginUser($userId);
		} catch (\Exception $e) {
			static::displayErrorPage($e->getMessage());
		}
		static::redirect($redirect);
	}
	
	/**
	 * Login user by ID
	 * 
	 * @param int $userId
	 * @throws \Exception
	 * @return string Redirection URL
	 */
	static protected function loginUser($userId) {
		if ($canLogin = apply_filters('cmreg_user_can_login', true, $userId)) {
			
			if ($user = get_userdata($userId)) {
				
				User::loginById($userId);
				
				$redirect = LoginController::getLoginRedirectUrl($user);
				if (empty($redirect)) {
					$redirect = site_url('/');
				}
				return $redirect;
				
			} else {
				throw new \Exception(Labels::getLocalized('cmreg_social_login_user_not_found'));
			}
			
		} else {
			throw new \Exception(Labels::getLocalized('cmreg_social_login_error_account_inactive'));
		}
	}
	
	static protected function redirect($url) {
		// For some reasing wp_redirect() doesn't work in the callback method so using header:
		header('Location: '. $url);
		exit;
	}
	
	static function wp_footer() {
		
		// Remove #_=_ characters from the URL
		
		echo '<script>if (window.location.hash == "#_=_") {
			history.replaceState 
		        ? history.replaceState(null, null, window.location.href.split("#")[0])
		        : window.location.hash = "";
		}
		</script>';
		
		if(is_user_logged_in()) {
			$user_id = get_current_user_id();
			$user_info = get_userdata($user_id);
			$first_name = $user_info->display_name;
			$last_name = get_user_meta($user_id, 'text-1591886535717', true);
			$firm_name = get_user_meta($user_id, 'text-1591959031546', true);
			$email = $user_info->user_email;
			echo '<script>
				jQuery(document).ready(function() {
					if(jQuery("form#wpforms-form-11412").length > 0) {
						if(jQuery("form#wpforms-form-11412 #wpforms-11412-field_19").val() == "") {
							jQuery("form#wpforms-form-11412 #wpforms-11412-field_19").val("'.trim($first_name.' '.$last_name).'");
						}
						if(jQuery("form#wpforms-form-11412 #wpforms-11412-field_1").val() == "") {
							jQuery("form#wpforms-form-11412 #wpforms-11412-field_1").val("'.$email.'");
						}
					}
					if(jQuery("form#gform_6").length > 0) {
						if(jQuery("form#gform_6 #input_6_1_3").val() == "") {
							jQuery("form#gform_6 #input_6_1_3").val("'.$first_name.'");
						}
						if(jQuery("form#gform_6 #input_6_1_6").val() == "") {
							jQuery("form#gform_6 #input_6_1_6").val("'.$last_name.'");
						}
						if(jQuery("form#gform_6 #input_6_11").val() == "") {
							jQuery("form#gform_6 #input_6_11").val("'.$firm_name.'");
						}
						if(jQuery("form#gform_6 #input_6_5").val() == "") {
							jQuery("form#gform_6 #input_6_5").val("'.$email.'");
						}
						if(jQuery("form#gform_6 #input_6_5_2").val() == "") {
							jQuery("form#gform_6 #input_6_5_2").val("'.$email.'");
						}
					}
					if(jQuery("form#gform_7").length > 0) {
						if(jQuery("form#gform_7 #input_7_1_3").val() == "") {
							jQuery("form#gform_7 #input_7_1_3").val("'.$first_name.'");
						}
						if(jQuery("form#gform_7 #input_7_1_6").val() == "") {
							jQuery("form#gform_7 #input_7_1_6").val("'.$last_name.'");
						}
						if(jQuery("form#gform_7 #input_7_11").val() == "") {
							jQuery("form#gform_7 #input_7_11").val("'.$firm_name.'");
						}
						if(jQuery("form#gform_7 #input_7_5").val() == "") {
							jQuery("form#gform_7 #input_7_5").val("'.$email.'");
						}
						if(jQuery("form#gform_7 #input_7_5_2").val() == "") {
							jQuery("form#gform_7 #input_7_5_2").val("'.$email.'");
						}
					}
				});
			</script>';
		}

		if(!is_user_logged_in()) {

			$name = '';
			if(isset($_GET['nm'])) {
				$name = $_GET['nm'];
			}
			$email = '';
			if(isset($_GET['em'])) {
				$email = $_GET['em'];
			}

			echo '<script>
				jQuery(document).ready(function() {
					if(jQuery("form#wpforms-form-11412").length > 0) {
						if(jQuery("form#wpforms-form-11412 #wpforms-11412-field_19").val() == "") {
							jQuery("form#wpforms-form-11412 #wpforms-11412-field_19").val("'.$name.'");
						}
						if(jQuery("form#wpforms-form-11412 #wpforms-11412-field_1").val() == "") {
							jQuery("form#wpforms-form-11412 #wpforms-11412-field_1").val("'.$email.'");
						}
					}
				});
			</script>';

			echo '<script>
			if(CMREG_Settings.isUserLoggedIn == "0") {
				if(CMREG_Settings.loginAuthenticationPopupEnable == "1" || (CMREG_Settings.loginAuthenticationInviteEnable == "1" && CMREG_Settings.loginAuthenticationInvite != "")) {
					if(CMREG_Settings.loginAuthenticationPopup == "1") {
						jQuery("body").append("<a href=\'#cmreg-only-login-click\' data-postpageid=\'"+CMREG_Settings.loginAuthenticationPopupPostID+"\' class=\'cmregAutoPopupTrigger\'></a>");
					} else if(CMREG_Settings.loginAuthenticationPopup == "2") {
						jQuery("body").append("<a href=\'#cmreg-only-registration-click\' data-postpageid=\'"+CMREG_Settings.loginAuthenticationPopupPostID+"\' class=\'cmregAutoPopupTrigger\'></a>");
					} else {
						jQuery("body").append("<a href=\'#cmreg-login-click\' data-postpageid=\'"+CMREG_Settings.loginAuthenticationPopupPostID+"\' class=\'cmregAutoPopupTrigger\'></a>");
					}
					setTimeout(function() {
						jQuery(".cmregAutoPopupTrigger").trigger("click");
						
						setTimeout(function() {
							if(CMREG_Settings.loginAuthenticationInvite != "") {
								jQuery("form.cmreg-registration-form").find("input[name=\'cmreg_invit_code\']").val(CMREG_Settings.loginAuthenticationInvite);
							}
						}, 2000);

						if(CMREG_Settings.loginAuthenticationPopupForce == "0") {
							setTimeout(function() {
								jQuery(".cmreg-overlay").off("click.cmreg");
								jQuery(".cmreg-overlay .cmreg-overlay-close").remove();
							}, 2000);
						}
				
					}, 1000);
				}
			}
			</script>';
		}

	}
	
	/**
	 * AJAX request endpoint after user entered an invitation code
	 */
	static function cmreg_social_login_invitation_code() {
		
		$response = array('success' => 0, 'msg' => 'Error');
		
		if ($nonce = filter_input(INPUT_POST, 'nonce') AND wp_verify_nonce($nonce, static::TRANSIENT_OAUTH_RESPONSE_PREFIX)) {
			if ($cacheKey = filter_input(INPUT_POST, 'cacheKey')) {
				if ($oauthResponse = get_transient(static::TRANSIENT_OAUTH_RESPONSE_PREFIX . $cacheKey)) {
					
					//error_log(print_r($oauthResponse, true));
					
					$code = filter_input(INPUT_POST, 'code');
					$invitationCodeRequired = (Settings::getOption(Settings::OPTION_REGISTER_INVIT_CODE) === Settings::INVITATION_CODE_REQUIRED);
					
					if (empty($code) AND $invitationCodeRequired) {
						$response['msg'] = Labels::getLocalized('social_login_invit_code_required_err');
					} else {
						
						// Use the passed invitation code in the further registration process
						add_filter(InvitationCodesController::FILTER_GET_INPUT_INVITATION_CODE, function($val) use ($code) {
							return $code;
						});
						
						// Register user
						$data = $oauthResponse;
						$provider = $data['auth']['provider'];
						$displayName = $data['auth']['info']['name'];
						$uid = $data['auth']['uid'];
						$email = (isset($data['auth']['info']['email']) ? $data['auth']['info']['email'] : null);
						
						try {
							
							// Register user
							$userId = static::registerUser($email, $uid, $provider, $displayName);
							
							// Login user
							try {
								$redirect = static::loginUser($userId);
							} catch (\Exception $e) {
								// Cannot login
								$redirect = site_url('/');
							}
							
							$response = array('success' => 1, 'msg' => Labels::getLocalized('social_login_register_success'), 'redirectUrl' => $redirect);
							
						} catch (\Exception $e) {
							$response['msg'] = Labels::getLocalized('social_login_register_error') . ' ' . $e->getMessage();
						}
						
					}
					
				} else $response['msg'] = 'Empty oauth response. Please try again.';
			} else $response['msg'] = 'Missing cache key field. Please try again.';
		} else $response['msg'] = 'Invalid nonce. Please try again.';
		
		header('Content-type: application/json');
		echo json_encode($response);
		exit;
		
	}

}