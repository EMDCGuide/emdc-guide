<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\model\Labels;
use com\cminds\registration\App;
use com\cminds\registration\model\Settings;
use com\cminds\registration\helper\Recaptcha;

class CaptchaController extends Controller {
	
	const FILTER_CATPCHA_ENABLED = 'cmreg_captcha_enabled';
	
	static $actions = array(
		'wp_head',
		'login_form' => array('args' => 1, 'priority' => 10000),
		'register_form' => array('args' => 1, 'priority' => 10000),
		'cmreg_register_form' => array('args' => 1, 'method' => 'register_form', 'priority' => 10000),
		'register_post' => array('args' => 3),
	);

	static $filters = array(
		'authenticate' => array('args' => 3, 'priority' => 100),
		'cmreg_options_config' => array('priority' => 50),
	);
	
	static function wp_head() {
		if (static::isCaptchaEnabledForLogin() OR static::isCaptchaEnabledForRegister()) {
			wp_enqueue_script('cmreg-recaptcha');
		}
	}
	
	static function login_form($place = null) {
		if (App::isLicenseOk() AND Recaptcha::isConfigured() AND static::isCaptchaEnabledForLogin()) {
			wp_enqueue_script('cmreg-recaptcha');
			if ($place == 'cmreg_overlay') {
				echo static::getCaptchaBlock();
			} else {
				echo '<div style="margin: 1em 0;" class="g-recaptcha" data-sitekey="'. esc_attr(Recaptcha::getSiteKey()) .'"></div>';
			}
		}
	}
	
	static function getCaptchaBlock() {
		return '<div class="cmreg-recaptcha cmreg-recaptcha-login" id="cmreg-recaptcha-'. rand() .'" data-sitekey="'
				. esc_attr(Recaptcha::getSiteKey()) .'"></div>';
	}
	
	static function authenticate($user, $username, $password) {
	
		if (!App::isLicenseOk()) return $user;
		
		$addError = function($errorCode, $msg) use (&$user) {
			if (is_wp_error($user)) {
				$user->add($errorCode, $msg);
			} else {
				$user = new \WP_Error($errorCode, $msg);
			}
		};
		
		if (static::isPostRequest() AND Recaptcha::isConfigured() AND static::isCaptchaEnabledForLogin()
				AND !RegistrationController::isRegistrationAction() // disable for signing-in after registration
				) {
			if (!Recaptcha::verify()) {
				$addError('invalid_captcha', Labels::getLocalized('login_error_invalid_captcha'));
			}
		}
	
		return $user;
	}
	
	/**
	 * Display extra field on the registration form.
	 *
	 * @param string $place
	 */
	static function register_form($place = null) {
		if (App::isLicenseOk() AND Recaptcha::isConfigured() AND Settings::getOption(Settings::OPTION_REGISTER_RECAPTCHA_ENABLE)) {
			$result = wp_enqueue_script('cmreg-recaptcha');
			if ($place == 'cmreg_overlay') {
				echo '<div class="cmreg-recaptcha cmreg-recaptcha-registration" id="cmreg-recaptcha-'. rand() .'" data-sitekey="'. esc_attr(Recaptcha::getSiteKey()) .'"></div>';
			} else {
				echo '<div style="margin: 1em 0;" class="g-recaptcha" data-sitekey="'. esc_attr(Recaptcha::getSiteKey()) .'"></div>';
			}
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
// 		var_dump(__METHOD__);
		if (App::isLicenseOk() AND Recaptcha::isConfigured() AND static::isPostRequest() AND static::isCaptchaEnabledForRegister()) {
			// Validate captcha
			if (!Recaptcha::verify()) {
				$errors->add('invalid_captcha', Labels::getLocalized('register_invalid_captcha_error'));
			}
		}
	}
	
	static function isCaptchaEnabled($option) {
		return apply_filters(static::FILTER_CATPCHA_ENABLED, Settings::getOption($option));
	}
	
	static function isCaptchaEnabledForLogin() {
		return static::isCaptchaEnabled(Settings::OPTION_LOGIN_RECAPTCHA_ENABLE);
	}
	
	static function isCaptchaEnabledForRegister() {
		return static::isCaptchaEnabled(Settings::OPTION_REGISTER_RECAPTCHA_ENABLE);
	}
	
	static function isPostRequest() {
		return ($_SERVER['REQUEST_METHOD'] === 'POST');
	}
	
	static function cmreg_options_config($config) {
		return array_merge($config, array(
			Settings::OPTION_RECAPTCHA_API_SITE_KEY => array(
				'type' => Settings::TYPE_STRING,
				'category' => 'general',
				'subcategory' => 'api',
				'title' => 'Google reCAPTCHA API site key',
				'desc' => '<a href="https://www.google.com/recaptcha/admin#list" target="_blank" class="button">Register new reCAPTCHA v2 key</a>'
			),
			Settings::OPTION_RECAPTCHA_API_SECRET_KEY => array(
				'type' => Settings::TYPE_STRING,
				'category' => 'general',
				'subcategory' => 'api',
				'title' => 'Google reCAPTCHA API secret key',
				'desc' => 'Enter <strong>reCAPTCHA v2</strong> keys only.<br>More details you can read in this documentation:<br><a href="https://creativeminds.helpscoutdocs.com/article/640-cm-registration-cmreg-general-settings" target="_blank">https://creativeminds.helpscoutdocs.com/article/640-cm-registration-cmreg-general-settings</a>',
			),
			Settings::OPTION_LOGIN_RECAPTCHA_ENABLE => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 0,
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Enable reCAPTCHA on the login form',
			),
			Settings::OPTION_LOGIN_TERMS_OF_SERVICE_CHECKBOX_TEXT => array(
				'type' => Settings::TYPE_RICH_TEXT,
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Terms of service acceptance text',
				'desc' => 'Enter text which will be displayed next to the checkbox that users have to check to accept terms of service. If left empty checkboxes will not be displayed.',
			),
			Settings::OPTION_REGISTER_RECAPTCHA_ENABLE => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 0,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Enable reCAPTCHA on the registration form',
			),
		));
	}

}