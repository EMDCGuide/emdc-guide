<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\App;
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\User;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\ProfileField;

class LoginController extends Controller {
	
	const LOGIN_NONCE = 'cmreg_login_nonce';
	const LOST_PASS_NONCE = 'cmreg_lost_pass_nonce';
	const SELF_REGISTER_NONCE = 'cmreg_self_register_nonce';
	
	static $actions = array(
		'wp_logout',
		'login_form_login',
		'login_form_lostpassword' => array('method' => 'login_form_login'),
		'retrieve_password_message' => array('priority' => 10, 'args' => 4),
		'password_change_email' => array('priority' => 10, 'args' => 3),
		'resetpass_form',
		'login_form_register',
		'login_init',
	);

	static $filters = array(
		'wishlistmember_login_redirect_override', // Fix conflict with WishList Member
	);

	static $ajax = array(
		'cmreg_login',
		'cmreg_lost_password',
		'cmreg_self_register'
	);
	
	static function login_init() {
		if (static::isAjax() AND filter_input(INPUT_POST, 'action') == 'cmreg_login') {
			static::cmreg_login();
		}
	}
	
	static function getLoginFormView($atts = array()) {
		if (!App::isLicenseOk()) return;
		FrontendController::includeAssets();
		$nonce = wp_create_nonce(self::LOGIN_NONCE);
		return self::loadFrontendView('login-form', compact('atts', 'nonce'));
	}
	
	static function getLostPasswordView($atts = array()) {
		return self::loadFrontendView('lost-password', $atts);
	}
	
	static function getSelfRegisterView($atts = array()) {
		return self::loadFrontendView('self-register', $atts);
	}

	static function cmreg_login() {
		
		if (!App::isLicenseOk()) return;
		
		$response = array('success' => false, 'msg' => Labels::getLocalized('login_error_msg'));
		
		/* Fix for S2Member Pro
 		register_shutdown_function(function() use (&$response) {
 			header('content-type: application/json');
 			echo json_encode($response);
 			exit;
 		});
		*/

		if (isset($_POST['nonce']) AND wp_verify_nonce($_POST['nonce'], self::LOGIN_NONCE) AND !empty($_POST['login']) AND !empty($_POST[ProfileField::REGISTRATION_FORM_ROLE_PASSWORD])) {
			$remember = (Settings::getOption(Settings::OPTION_LOGIN_REMEMBER_ENABLE) AND !empty($_POST['remember']));
			$redirectUrl = filter_input(INPUT_POST, 'cmreg_redirect_url');
			try {
				$user = User::login($_POST['login'], $_POST[ProfileField::REGISTRATION_FORM_ROLE_PASSWORD], $remember);
				$response = array(
					'success' => true,
					'msg' => Labels::getLocalized('login_success_msg'),
					'redirect' => (static::getLoginRedirectUrl($user, $redirectUrl) ?: 'reload'),
				);
			} catch (\Exception $e) {
				$response['msg'] = $e->getMessage();
			}
		} else {
			
			if(!is_admin()) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			
			if ( is_plugin_active('cm-secure-login/plugin.php') || is_plugin_active('cm-secure-login-pro/plugin.php')) {
				if(get_option('cmlog_disable_passwords_all_users', 0) == 1) {
					$code = $_POST['cmlog_code'];
					$user = get_user_by('email', $_POST['login']);
					if($user->ID) {
						$savedCode = get_user_meta($user->ID, 'cmlog_login_verification_code_string', true);
						if($savedCode == $code) {
							wp_clear_auth_cookie();
							wp_set_auth_cookie($user->ID);
							User::updateLastActivity();
							update_user_meta($user->ID, 'last_login', time());
							$redirectUrl = filter_input(INPUT_POST, 'cmreg_redirect_url');
							$response = array(
								'success' => true,
								'msg' => Labels::getLocalized('login_success_msg'),
								'redirect' => (static::getLoginRedirectUrl($user, $redirectUrl) ?: 'reload'),
							);
						} else {
							$response = array('success' => false, 'msg' => get_option('cmlog_label_login_error_invalid_code', 'Invalid verification code.'));
						}
					}
				}
			}

		}
		
		$response = apply_filters('cmreg_login_ajax_response', $response);
		header('content-type: application/json');
		echo json_encode($response);
		exit;
		
	}
	
	static function getLoginRedirectUrl(\WP_User $user, $url = null) {
		
		global $wpdb;

		if (empty($url)) {
			if ($url = User::getCustomAfterLoginUrl($user->ID)) { // Redirection per role
				return $url;
			} else {
				$url = Settings::getOption(Settings::OPTION_LOGIN_REDIRECT_URL);
				//if (empty($url)) {
				//	$url = site_url();
				//}
			}
		}
		
		if ($user) {
			$url = str_replace('%userlogin%', $user->user_login, $url);
			$url = str_replace('%usernicename%', $user->user_nicename, $url);

			$primary_blog = get_user_meta($user->ID, 'primary_blog', true);
			if ($primary_blog != '') {

				$table_name = $wpdb->base_prefix.$primary_blog.'_options';
				$query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));
				if ($wpdb->get_var($query) == $table_name) {
					$sitelinkobj = $wpdb->get_row("SELECT * FROM ".$table_name." WHERE option_name = 'siteurl'");
					$sitelink = $sitelinkobj->option_value;
					$sitelinkarr = explode("/", $sitelink);
					foreach($sitelinkarr as $key => $value) {
						if(empty($value)) {
							unset($sitelinkarr[$key]);
						}
					}
					$sitelinkfarr = array_values($sitelinkarr);
					$url = str_replace('%usersitename%', end($sitelinkfarr), $url);
				} else {
					$url = str_replace('%usersitename%', '', $url);
				}

			} else {
				$url = str_replace('%usersitename%', '', $url);
			}

		}
		
		if($url != '') {
			if(strpos($url, "http") === false) {
				$url = site_url().$url;
			}
		}
		return $url;
	}
	
	static function wp_logout() {
		if ($url = Settings::getOption(Settings::OPTION_LOGOUT_REDIRECT_URL)) {
			wp_redirect($url);
			exit;
		}
	}

	static function cmreg_lost_password() {
		if (!App::isLicenseOk()) return;
		$response = array('success' => false, 'msg' => Labels::getLocalized('lost_pass_error_msg'));
		if (Settings::getOption(Settings::OPTION_LOGIN_LOST_PASSWORD_ENABLE)) {
			if (isset($_POST['nonce']) AND wp_verify_nonce($_POST['nonce'], self::LOST_PASS_NONCE) AND !empty($_POST['email'])) {

				if(is_email($_POST['email'])) {
					$user = get_user_by( 'email', $_POST['email'] );
					if(!$user) {
						$user = get_user_by( 'login', $_POST['email'] );
					}
				} else {
					$user = get_user_by( 'login', $_POST['email'] );
				}

				if (!empty($user) && !is_wp_error($user)) {
					$res = User::lostPasswordEmail($user);
					//echo "<pre>"; print_r($res); echo "<pre>"; 
					if ($res === true) {
						delete_user_meta($user->ID, 'salt');
						$response = array('success' => true, 'msg' => Labels::getLocalized('lost_pass_email_sent_msg'));
					} else $response['msg'] = Labels::getLocalized('cannot_send_email');
				} else $response['msg'] = Labels::getLocalized('user_not_found');
			} else $response['msg'] = Labels::getLocalized('invalid_nonce');
		} else $response['msg'] = Labels::getLocalized('feature_disabled');
		header('content-type: application/json');
		echo json_encode($response);
		exit;
	}

	static function cmreg_self_register() {
		if (!App::isLicenseOk()) return;
		$response = array('success' => false, 'msg' => Labels::getLocalized('self_register_error_msg'));
		if (isset($_POST['nonce']) AND wp_verify_nonce($_POST['nonce'], self::SELF_REGISTER_NONCE)) {
			$self_register_api_url = Settings::getOption(Settings::OPTION_SELF_REGISTER_API_URL);
			if($self_register_api_url == '') {
				$response['msg'] = Labels::getLocalized('self_register_api_not_found');
			} else {

				$first_name = $_POST['first_name'];
				$last_name = $_POST['last_name'];
				$email = $_POST['email'];
				$phone = (isset($_POST['phone']) && $_POST['phone'] != '')?$_POST['phone']:'';
				$social_security = $_POST['social_security'];
				$password = sha1(microtime().mt_rand()).'Az.123';
				//$email_arr = explode("@", $email);
				//$login = $email_arr[0];
				$login = $email;
				$displayName = $first_name.' '.$last_name;
				$role = Settings::getOption(Settings::OPTION_REGISTER_DEFAULT_ROLE);

				$url_str = '';
				if(strlen($social_security) > 4) {
					$url_str .= '?id='.$social_security;
				} else {
					$url_str .= '?lastFourDigits='.$social_security;
				}
				$url_str .='&firstname='.$first_name;
				$url_str .='&lastname='.$last_name;

				$api_content_json = file_get_contents($self_register_api_url.$url_str);
				$api_content = json_decode($api_content_json, true);
				if(isset($api_content['Message']) && $api_content['Message'] != '') {
					if($api_content['Message'] == 'Member Not Found') {
						$response['msg'] = Labels::getLocalized('self_register_error_msg');
					} else {
						$response['msg'] = $api_content['Message'];
					}
				} else {
					$member_id = $api_content[0]['Id'];

					$userId = User::register($email, $password, $login, $displayName, $role);
					update_user_meta($userId, 'first_name', $first_name);
					update_user_meta($userId, 'last_name', $last_name);
					update_user_meta($userId, 'phone', $phone);
					update_user_meta($userId, 'social_security', $social_security);
					update_user_meta($userId, 'api_member_id', $member_id);

					$response = array('success' => true, 'msg' => Labels::getLocalized('self_register_success'));
				}

			}
		} else {
			$response['msg'] = Labels::getLocalized('self_register_invalid_nonce');
		}
		header('content-type: application/json');
		echo json_encode($response);
		exit;
	}
	
	static function debugActions() {
		global $wp_filter;
		echo '<pre>';
		foreach ($wp_filter as $actionName => $names) {
			foreach ($names as $priority => $filters) {
				foreach ($filters as $name => $filter) {
					echo PHP_EOL . '-----------------------------' . PHP_EOL;
					echo $actionName . PHP_EOL;
					//echo '-----------------------------' . PHP_EOL;
					if (is_array($filter['function'])) {
						if (is_object($filter['function'][0])) {
							echo get_class($filter['function'][0]);
						} else {
							echo $filter['function'][0];
						}
						echo $filter['function'][1];
					} else {
						var_dump($filter['function']);
					}
					echo PHP_EOL;
				}
			}
		}
	}
	
	static function login_form_login() {
		if ($url = Settings::getOption(Settings::OPTION_WP_LOGIN_PAGE_REDIRECTION_URL)) {
			if (!isset($_REQUEST['interim-login'])) {
				wp_redirect($url);
				exit;
			}
		}
	}

	static function password_change_email($pass_change_email, $user, $userdata) {
		if(Settings::getOption(Settings::OPTION_REGISTER_EMAIL_VERIFICATION_ON_SECONDARY_EMAIL_ENABLE) == '1') {
			if(Settings::getOption(Settings::OPTION_REGISTER_SECONDARY_EMAIL_FIELD_META_KEY) != '') {
				$email = get_user_meta($user['ID'], Settings::getOption(Settings::OPTION_REGISTER_SECONDARY_EMAIL_FIELD_META_KEY), true);
				$pass_change_email['to'] = $email;
			}
		}
		return $pass_change_email;
	}

	static function retrieve_password_message($message, $key, $user_login, $user_data) {
		$url = Settings::getOption(Settings::OPTION_WP_LOSTPASSWORD_PAGE_REDIRECTION_URL);
		$msg = Settings::getOption(Settings::OPTION_RESET_PASSWORD_EMAIL_BODY);
		$msg = str_replace('[userfirstname]', get_user_meta($user_data->ID, 'first_name', true), $msg);
		$msg = str_replace('[userlastname]', get_user_meta($user_data->ID, 'last_name', true), $msg);
		$msg = str_replace('[userlogin]', $user_login, $msg);
		$msg = str_replace('[siteurl]', site_url('/'), $msg);
		if($url != '') {
			$msg = str_replace('[resetpasswordurl]', $url."?key=$key&login=".base64_encode(rawurlencode($user_login)), $msg);
		} else {
			$msg = str_replace('[resetpasswordurl]', site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user_login),'login'), $msg);
		}
		return $msg;
	}

	static function resetpass_form() {
		if ($url = Settings::getOption(Settings::OPTION_WP_LOSTPASSWORD_PAGE_REDIRECTION_URL)) {
			if (!isset($_REQUEST['interim-login'])) {
				wp_redirect($url);
				exit;
			}
		}
	}
	
	static function login_form_register() {
		if ($url = Settings::getOption(Settings::OPTION_WP_REGISTER_PAGE_REDIRECTION_URL)) {
			wp_redirect($url);
			exit;
		}
	}
	
	/**
	 * Fix conflict with the WishList Member plugin
	 * @param boolean $result
	 * @return boolean
	 */
	static function wishlistmember_login_redirect_override($result) {
		return true;
	}
	
	static function authenticateAddError($errorCode, $msg, &$user) {
		if(wp_doing_ajax()) {
			$user = new \WP_Error($errorCode, $msg);
		} else {
			if (is_wp_error($user)) {
				$user->add($errorCode, $msg);
			} else {
				$user = new \WP_Error($errorCode, $msg);
			}
		}
	}
	
}