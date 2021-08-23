<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\helper\Nonce;
use com\cminds\registration\App;
use com\cminds\registration\lib\Email;
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\User;
use com\cminds\registration\model\ProfileField;

class RegistrationController extends Controller {
	
	const NONCE_REGISTRATION = 'cmreg_registration_nonce';
	
	const FIELD_ROLE = 'role';
	const FIELD_ROLE_NONCE = 'role_nonce';
	
	static $actions = array(
		'register_new_user' => array('args' => 1, 'priority' => 800),
		'cmreg_send_welcome_email' => array('args' => 1),
	);

	static $ajax = array(
		'cmreg_registration'
	);

	static function getRegistrationFormView($atts) {
		$nonce = Nonce::create(static::NONCE_REGISTRATION);
		$roleNonce = (empty($atts['role']) ? '' : Nonce::create(static::NONCE_REGISTRATION . $nonce . $atts['role']));
		return self::loadFrontendView('registration-form', compact('nonce', 'atts', 'roleNonce'));
	}

	static function cmreg_registration() {
		
		if (!App::isLicenseOk()) return;
		
		$userId = null;
		$response = array('success' => false, 'msg' => Labels::getLocalized('register_error_msg'));
		
		$email = static::getRegistrationFieldValue(ProfileField::REGISTRATION_FORM_ROLE_EMAIL);
		$login = static::getRegistrationFieldValue(ProfileField::REGISTRATION_FORM_ROLE_USERNAME);
		$password = static::getRegistrationFieldValue(ProfileField::REGISTRATION_FORM_ROLE_PASSWORD);

		$register_pass_enable = Settings::getOption(Settings::OPTION_REGISTER_PASS_ENABLE);
		
		if ( (static::isRegistrationAction() AND (!empty($email) || !empty($login)) AND !empty($password)) ||
			 (static::isRegistrationAction() AND (!empty($email) || !empty($login)) AND empty($password) AND $register_pass_enable == '0') ) {
			
			//$login = (empty($_POST[ProfileField::REGISTRATION_FORM_ROLE_USERNAME]) ? '' : $_POST[ProfileField::REGISTRATION_FORM_ROLE_USERNAME]);
			$email = (is_email($email) ? $email : null);
			$displayName = static::getRegistrationFieldValue(ProfileField::REGISTRATION_FORM_ROLE_DISPLAY_NAME);
			//$displayName = (!empty($_POST[ProfileField::REGISTRATION_FORM_ROLE_DISPLAY_NAME]) ? $_POST[ProfileField::REGISTRATION_FORM_ROLE_DISPLAY_NAME] : (!empty($_POST[ProfileField::REGISTRATION_FORM_ROLE_USERNAME]) ? $_POST[ProfileField::REGISTRATION_FORM_ROLE_USERNAME] : $email));
			
			if(isset($_POST['cmreg_extra_field']['input-package-role'])) {
				$role = $_POST['cmreg_extra_field']['input-package-role'];
			} else {
				$role = filter_input(INPUT_POST, static::FIELD_ROLE);
			}
			
			$redirect_url = filter_input(INPUT_POST, 'cmreg_redirect_url');
			if($redirect_url != '') {
				if(strpos($redirect_url, "http") === false) {
					$redirect_url = site_url().$redirect_url;
				}
			}

			try {
				
				if (!static::verifyRoleNonce()) {
					throw new \Exception(Labels::getLocalized('register_role_nonce_error'));
				}
				
				if (Settings::getOption(Settings::OPTION_REGISTER_REPEAT_EMAIL_ENABLE)) {
					$repeatEmail = static::getRegistrationFieldValue(ProfileField::REGISTRATION_FORM_ROLE_EMAIL_REPEAT);
					if (empty($repeatEmail) OR $repeatEmail !== $email) {
						throw new \Exception(Labels::getLocalized('register_repeat_email_error_msg'));
					}
				}

				if (Settings::getOption(Settings::OPTION_REGISTER_REPEAT_PASS_ENABLE)) {
					$repeatPassword = static::getRegistrationFieldValue(ProfileField::REGISTRATION_FORM_ROLE_PASSWORD_REPEAT);
					if (empty($repeatPassword) OR $repeatPassword !== $password) {
						throw new \Exception(Labels::getLocalized('register_repeat_pass_error_msg'));
					}
				}
				
				// The email and other params will be validated inside this method and throw exception if invalid:
				
				$userId = User::register($email, $password, $login, $displayName, $role);
				$user = get_userdata($userId);
				
				$after_register_autologin_enable = Settings::getOption(Settings::OPTION_AFTER_REGISTER_AUTOLOGIN_ENABLE);

				$response = array(
					'success' => true,
					'msg' => Labels::getLocalized('register_success_msg'),
				);
				
				if(!is_admin()) {
					include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}
				if ( is_plugin_active( 'cm-registration-approve-new-users-addon/plugin.php' ) ) {
					if(get_option('cmreg_anu_approve_registration_enable', 0) == '1' && $redirect_url != '') {
						$response['redirect'] = $redirect_url;
					}
				} else {
					if($redirect_url != '') {
						$response['redirect'] = $redirect_url;
					}
				}

				$canLogin = apply_filters('cmreg_user_can_login', true, $userId);
				if ($after_register_autologin_enable && $canLogin) {
					try {
						add_filter('cmloc_login_verification_enabled', '__return_false', 1000);
						User::login($user->user_login, $password, false);
						$registerurl = Settings::getOption(Settings::OPTION_REGISTER_REDIRECT_URL);
						if ($urlAfterLogin = LoginController::getLoginRedirectUrl($user, $registerurl)) {
							$response['redirect'] = $urlAfterLogin;
						} else {
							if($redirect_url != '') {
								$response['redirect'] = $redirect_url;
							} else {
								$response['redirect'] = 'reload';
							}
						}
					} catch (\Exception $e) {
						
					}
				} else {
					User::logout();
					
				}
				
				/*
				$status = User::getEmailVerificationStatus($userId);
				if (User::EMAIL_VERIFICATION_STATUS_PENDING == $status) {
					User::logout();
					$response = array(
						'success' => true,
 						'msg' => Labels::getLocalized('register_verification_msg'),
 					);
 				} else {
 					User::login($email, $_POST[ProfileField::REGISTRATION_FORM_ROLE_PASSWORD], false);
 					$urlAfterLogin = LoginController::getLoginRedirectUrl($user);
 					$response = array(
 						'success' => true,
 						'msg' => Labels::getLocalized('register_success_msg'),
 						'redirect' => (empty($urlAfterLogin) ? 'reload' : $urlAfterLogin),
 					);
 				}
				*/
				
			} catch (\Exception $e) {
				$response['msg'] = $e->getMessage();
			}
			
		}

		$response = apply_filters('cmreg_registration_ajax_response', $response, $userId);
		
		header('content-type: application/json');
		echo json_encode($response);
		exit;
	}

	static protected function getRegistrationFieldValue($role) {
		//error_log($role);
		if ($field = ProfileField::getFieldByRegistrationFormRole($role)) {
			$metaName = $field->getUserMetaKey();
			//error_log($metaName);
			if (isset($_POST[ProfileFieldController::POST_FIELDS_ARR]) AND isset($_POST[ProfileFieldController::POST_FIELDS_ARR][$metaName])) {
				$value = $_POST[ProfileFieldController::POST_FIELDS_ARR][$metaName];
				return $value;
			}
			//else error_log('no post value');
		}
		//else error_log('nofield');
	}

	static function verifyRoleNonce() {
		$role = filter_input(INPUT_POST, static::FIELD_ROLE);
		$roleNonce = filter_input(INPUT_POST, static::FIELD_ROLE_NONCE);
		$nonce = filter_input(INPUT_POST, 'nonce');
		if (!empty($role) AND (empty($roleNonce) OR !Nonce::verify(static::NONCE_REGISTRATION . $nonce . $role, $roleNonce))) {
			return false;
		} else {
			return true;
		}
	}

	static function isRegistrationAction() {
		return (isset($_POST['nonce'])
			//AND wp_verify_nonce($_POST['nonce'], static::NONCE_REGISTRATION)
			AND Nonce::verify(static::NONCE_REGISTRATION, $_POST['nonce'])
		);
	}

	/**
	 * After successful registration
	 * 
	 * @param unknown $userId
	 */
	static function register_new_user($userId) {
		if (!App::isLicenseOk()) return;
		if (User::EMAIL_VERIFICATION_STATUS_VERIFIED == User::getEmailVerificationStatus($userId) AND apply_filters('cmreg_allow_send_welcome_email', true, $userId)) {
			$approval_status = get_user_meta($userId, 'cmreganu_approval_status', true);
			if($approval_status == '' || $approval_status == 'approved') {
				static::cmreg_send_welcome_email($userId);
			}
		}
	}

	static function cmreg_send_welcome_email($userId) {
		if(Settings::getOption(Settings::OPTION_REGISTER_WELCOME_EMAIL_ENABLE)) {
			Email::sendWelcomeEmail($userId);
		}
	}
	
	static function getWelcomeUrl($userId) {	
		if ($url = User::getCustomAfterLoginUrl($userId)) { // Redirection per role
			return $url;
		}
		else if ($pageId = Settings::getOption(Settings::OPTION_REGISTER_WELCOME_PAGE)) { // Default welcome page
			return get_permalink($pageId);
		} else { // Home page
			return site_url();
		}
	}
	
}