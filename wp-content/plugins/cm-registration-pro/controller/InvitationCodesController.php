<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\model\Labels;
use com\cminds\registration\model\User;
use com\cminds\registration\model\Settings;
use com\cminds\registration\lib\Email;
use com\cminds\registration\model\InvitationCode;
use com\cminds\registration\App;
use com\cminds\registration\shortcode\CreateInvitationCodeShortcode;

class InvitationCodesController extends Controller {
	
	const PARAM_INVITATION_CODE = 'cmreg_code';
	const PARAM_ACTION = 'cmreg_action';
	const COOKIE_INVITATION_CODE = 'cmreg_invitation_code';
	const ACTION_USER_CREATE_CODE = 'cmreg_user_create_invitation_code';
	const FIELD_INVITATION_CODE = 'cmreg_invit_code';
	const FILTER_GET_INPUT_INVITATION_CODE = 'cmreg_get_input_invitation_code';
	
	static $filters = array(
		'cmreg_options_config' => array('priority' => 50),
		'cmreg_create_invitation_code' => array('args' => 2),
	);

	static $actions = array(
		'plugins_loaded',
		//'register_form' => array('args' => 1),
		//'cmreg_register_form' => array('args' => 1, 'method' => 'register_form', 'priority' => 200),
		'register_post' => array('args' => 3),
		'register_new_user' => array('args' => 1, 'priority' => 20),
		'cmreg_profile_edit_form' => array('args' => 1, 'priority' => 100),
	);

	static $ajax = array(
		'cmreg_user_create_invitation_code',
	);
	
	static function plugins_loaded() {
		// Set cookie with the invitation code to further use
		$code = filter_input(INPUT_GET, static::PARAM_INVITATION_CODE);
		if (strlen($code) > 0) {
			setcookie(static::COOKIE_INVITATION_CODE, $code, time() + 3600*24*365);
		}
	}
	
	/**
	 * Display extra field on the registration form.
	 *
	 * @param string $place
	 */
	static function register_form($place = null) {
		if (App::isLicenseOk()) {
			// This has been moved to the profile fields
			// echo self::getInvitationCodeField();
		}
	}
	
	static function getInvitationCodeField($invitationCodeLabel='', $invitationCodePlaceholder='', $invitationCodeTooltip='') {
		$invitationCodeRequired = (Settings::getOption(Settings::OPTION_REGISTER_INVIT_CODE) == Settings::INVITATION_CODE_REQUIRED);
		$invitationCode = (empty($_GET[self::PARAM_INVITATION_CODE]) ? '' : $_GET[self::PARAM_INVITATION_CODE]);
		$showInvitationCode = (Settings::getOption(Settings::OPTION_REGISTER_INVIT_CODE) != Settings::INVITATION_CODE_DISABLED);
		if ($showInvitationCode) {
			return self::loadFrontendView('registration', compact('invitationCodeRequired', 'invitationCode', 'invitationCodeLabel', 'invitationCodePlaceholder', 'invitationCodeTooltip'));
		}
	}
	
	/**
	 * Validate the registration
	 *
	 * @param string $sanitized_user_login
	 * @param string $user_email
	 * @param \WP_Error $errors
	 */
	static function register_post($sanitized_user_login, $user_email, \WP_Error $errors) {
		if (App::isLicenseOk()) {
			
			$codeRequired = (Settings::getOption(Settings::OPTION_REGISTER_INVIT_CODE) == Settings::INVITATION_CODE_REQUIRED);
			
			// Validate invitation code before registration
			$invitationCode = static::getInputInvitationCode();
			//error_log(__METHOD__ . ' ----- ' . $invitationCode);exit;
			
			/**
			 * @var $code InvitationCode
			 */
			$code = InvitationCode::getByCode($invitationCode);
			
			if (empty($invitationCode) OR empty($code) OR $code->getStatus() != 'publish') {
				
				// Code doesn't exists
				
				if ($codeRequired) { // but it's required
					$errors->add('invalid_invitation_code', Labels::getLocalized('register_invit_code_invalid_error'));
				}
				
			} else {
				
				// Code exists
				
				$requireEmail = $code->getRequiredEmail();
				//var_dump($requireEmail);var_dump($user_email);exit;
				if (!empty($requireEmail) AND strtolower($requireEmail) != strtolower($user_email)) {
					$errors->add('expected_different_email', Labels::getLocalized('register_invit_code_expected_different_email_error'));
				}
				
				$usersLimit = $code->getUsersLimit();
				if (!empty($usersLimit) AND $usersLimit <= $code->getUsersCount()) {
					$errors->add('invit_code_users_limit_error', Labels::getLocalized('register_invit_code_users_limit_error'));
				}
				
				$expiration = $code->getExpirationDate();
				if (!empty($expiration) AND $expiration < time()) {
					$errors->add('invit_code_expired_error', Labels::getLocalized('register_invit_code_expired_error'));
				}
				
			}
			
		}
	}
	
	static function getInputInvitationCode() {
		$invitationCode = apply_filters(static::FILTER_GET_INPUT_INVITATION_CODE, filter_input(INPUT_POST, static::FIELD_INVITATION_CODE));
		return $invitationCode;
	}
	
	/**
	 * After successful registration
	 *
	 * @param unknown $userId
	 */
	static function register_new_user($userId) {
		
		if (!App::isLicenseOk()) return;
		
		$invitationCode = static::getInputInvitationCode();
		$code = InvitationCode::getByCode($invitationCode);
		if ($code) {
			$code->registerInvitation($userId);
		}
		
	}
	
	static function cmreg_options_config($config) {
		return array_merge($config, array(
			Settings::OPTION_REGISTER_INVIT_CODE => array(
				'type' => Settings::TYPE_RADIO,
				'options' => array(
					Settings::INVITATION_CODE_DISABLED => 'disabled',
					Settings::INVITATION_CODE_OPTIONAL => 'optional',
					Settings::INVITATION_CODE_REQUIRED => 'required',
				),
				'default' => Settings::INVITATION_CODE_OPTIONAL,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Ask for invitation code',
			),
		));
	}
	
	static function cmreg_create_invitation_code($result, $args) {
		
		$result = array();
		
		$post = array(
			'ID' => null,
			'post_title' => $args['name'],
			'post_type' => InvitationCode::POST_TYPE,
			'post_status' => 'publish',
		);
		$obj = new InvitationCode((object)$post);
		if ($obj->save()) {
			$obj->setCodeString(isset($args['code']) ? $args['code'] : $obj->getOrGenerateCodeString());
			if (isset($args['role'])) {
				$obj->setUserRole($args['role']);
			}
			if (isset($args['usersLimit']) AND is_numeric($args['usersLimit']) AND $args['usersLimit'] > 0) {
				$obj->setUsersLimit($args['usersLimit']);
			}
			if (isset($args['expirationDate'])) {
				$obj->setExpirationDate($args['expirationDate']);
			}
			if (isset($args['requiredEmail'])) {
				$obj->setRequiredEmail($args['requiredEmail']);
			}
			if (isset($args['emailVerification'])) {
				$obj->setEmailVerificationStatus($args['emailVerification']);
			}
			$result['ID'] = $obj->getId();
			$result['codeString'] = $obj->getCodeString();
			$result['instance'] = $obj;
		} else {
			$result = false;
		}
		
		return $result;
		
	}
	
	static function cmreg_user_create_invitation_code() {
		$response = array('success' => false, 'msg' => "An error occurred.");
		$nonce = filter_input(INPUT_POST, 'nonce');
		$hash = filter_input(INPUT_POST, 'hash');
		$email = filter_input(INPUT_POST, 'email');
		$showlink = filter_input(INPUT_POST, 'showlink');
		if (wp_verify_nonce($nonce, static::ACTION_USER_CREATE_CODE) AND $hash) {
			$atts = CreateInvitationCodeShortcode::getAttributesByHash($hash);
			if (!empty($atts) AND is_array($atts)) {
				
				$current_user = wp_get_current_user();

				$post = array(
					'ID' => null,
					'post_title' => 'Created by '.$current_user->user_login.' user',
					'post_type' => InvitationCode::POST_TYPE,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
				);
				$obj = new InvitationCode((object)$post);
				if ($obj->save()) {
					
					$codeString = $response['codeString'] = $obj->getOrGenerateCodeString();
					
					if (!empty($atts['role'])) {
						$obj->setUserRole($atts['role']);
					}
					if (!empty($atts['userslimit']) AND is_numeric($atts['userslimit']) AND $atts['userslimit'] > 0) {
						$obj->setUsersLimit($atts['userslimit']);
					}
					if (!empty($atts['expiration'])) {
						$obj->setExpirationDate($atts['expiration']);
					}
					if (!empty($atts['verifyemail']) AND $atts['verifyemail'] != 'global') {
						$obj->setEmailVerificationStatus($atts['verifyemail']);
					}
					if (!empty($atts['emailinput']) AND !empty($email) AND is_email($email)) {
						$obj->setRequiredEmail($email);
						static::sendInvitationToEmail($obj, $email, get_current_user_id());
					}
					
					$link = Settings::getOption(Settings::OPTION_INVITE_FRIEND_REGISTRATION_PAGE_URL);
					if (empty($link)) $link = site_url();
					$link = add_query_arg(array(static::PARAM_INVITATION_CODE => $codeString), $link);

					$response = array(
						'success' => true,
						'html' => static::loadFrontendView('create-invitation-code-result', compact('codeString', 'link', 'showlink')),
					);
					
				} else {
					$response['msg'] = 'Cannot create the invitation code. Please try again.';
				}
				
			} else {
				$response['msg'] = 'Cannot perform the action. Please try again.';
			}
		}
		
		header('content-type: application/json');
		echo json_encode($response);
		exit;
		
	}
	
	static protected function sendInvitationToEmail(InvitationCode $code, $invitedEmail, $inviterUserId) {
		$subject = Settings::getOption(Settings::OPTION_INVITE_FRIEND_EMAIL_SUBJECT);
		$body = wpautop(Settings::getOption(Settings::OPTION_INVITE_FRIEND_EMAIL_BODY));
		$link = Settings::getOption(Settings::OPTION_INVITE_FRIEND_REGISTRATION_PAGE_URL);
		if (empty($link)) $link = site_url();
		$link = add_query_arg(array(static::PARAM_INVITATION_CODE => $code->getCodeString()), $link);
		$vars = array_merge(array('[linkurl]' => $link, '[invitationcode]' => $code->getCodeString()), Email::getBlogVars(), Email::getUserVars($inviterUserId));
		return Email::send($invitedEmail, $subject, $body, $vars);
	}
	
	static function cmreg_profile_edit_form($userId) {
		if (Settings::getOption(Settings::OPTION_USER_PROFILE_INVIT_CODE_SHOW)) {
			$code = InvitationCode::getByUser($userId);
			echo static::loadFrontendView('profile-edit-invitation-code', compact('code', 'userId'));
		}
	}

}