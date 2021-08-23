<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\shortcode\LoginFormShortcode;
use com\cminds\registration\shortcode\RegistrationFormShortcode;
use com\cminds\registration\model\Settings;
use com\cminds\registration\App;
use com\cminds\registration\model\Labels;

class FrontendController extends Controller {
	
	static $actions = array(
		'wp_head',
		'wp_enqueue_scripts' => array('method' => 'includeAssets'),
		'login_enqueue_scripts' => array('method' => 'includeAssets'),
	);

	static $ajax = array(
		'cmreg_login_overlay'
	);
	
	static function includeAssets() {

		global $post;
		
		if (!App::isLicenseOk()) return;
		if (static::isAjax()) return;
		
		wp_enqueue_style('cmreg-frontend');
		
		$exclude_urls = '';
		if(Settings::getOption(Settings::OPTION_LOGIN_EXCLUDE_REDIRECT_URL) != '') {
			$exclude_urls = preg_split( '/\r\n|\r|\n/', Settings::getOption(Settings::OPTION_LOGIN_EXCLUDE_REDIRECT_URL));
		}
		
		if (wp_script_is( 'cmreg-frontend', 'enqueued' )) {
			return;
		} else {
			
			
			if(get_option('show_on_front') == 'posts' && is_home()) {
				$loginAuthenticationPopupEnable = '0';
			} else {
				if(isset($post->ID) && get_post_meta($post->ID, 'cmreg_login_access', true)) {
					$loginAuthenticationPopupEnable = '1';
				} else {
					$loginAuthenticationPopupEnable = '0';
				}
			}

			wp_localize_script('cmreg-frontend', 'CMREG_Settings', array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'isUserLoggedIn' => intval(is_user_logged_in()),
				'logoutUrl' => wp_logout_url(),
				'logoutButtonLabel' => Labels::getLocalized('logout_button'),
				'overlayPreload' => intval(Settings::getOption(Settings::OPTION_OVERLAY_PRELOAD)),
				'globalSiteAccess' => Settings::getOption(Settings::OPTION_LOGIN_GLOBAL_SITE_ACCESS),
				'customRedirectUrl' => Settings::getOption(Settings::OPTION_LOGIN_CUSTOM_REDIRECT_URL),
				'excludeRedirectUrl' => $exclude_urls,
				'siteHomePageRedirectUrl' => site_url(),
				'loginAuthenticationPopupEnable' => $loginAuthenticationPopupEnable,
				'loginAuthenticationPopupPostID' => (isset($post->ID) && get_post_meta($post->ID, 'cmreg_login_access', true))?$post->ID:'0',
				'loginAuthenticationInviteEnable' => Settings::getOption(Settings::OPTION_INVITE_AUTO_POPUP_ENABLE),
				// 'loginAuthenticationInvite' => (isset($_GET['invite']) && $_GET['invite'] != '')?$_GET['invite']:(isset($_GET['cmreg_code']) && $_GET['cmreg_code'] != '')?$_GET['cmreg_code']:'',
				'loginAuthenticationInvite' => (isset($_GET['invite']) && $_GET['invite'] != '')?$_GET['invite']:((isset($_GET['cmreg_code']) && $_GET['cmreg_code'] != '')?$_GET['cmreg_code']:''),

				'loginAuthenticationPopup' => Settings::getOption(Settings::OPTION_LOGIN_AUTHENTICATION_POPUP),
				'loginAuthenticationPopupForce' => Settings::getOption(Settings::OPTION_LOGIN_AUTHENTICATION_POPUP_FORCE),
			));
			wp_enqueue_script('cmreg-frontend');
			//error_log( print_r($_GET, true) );
		}
	}
	
	static function wp_head() {
		echo '<style type="text/css">';
		if ($css = Settings::getOption(Settings::OPTION_CUSTOM_CSS)) {
			echo $css;
		}
		$opacity = Settings::getOption(Settings::OPTION_OVERLAY_OPACITY);
		if (!is_numeric($opacity) OR !$opacity) {
			$opacity = 70;
		}
		echo PHP_EOL;
		echo '.cmreg-overlay {background-color: rgba(0,0,0,'. ($opacity/100) .') !important;}' . PHP_EOL;
		echo '.cmreg-loader-overlay {background-color: rgba(0,0,0,'. ($opacity/100) .') !important;}' . PHP_EOL;
		echo '</style>';
	}
	
	static function getOverlayView($atts = array()) {
		//$content = LoginController::getLoginFormView($atts) . RegistrationController::getRegistrationFormView($atts);
		
		if($atts['post_page_id'] == '0') {
			$content = LoginFormShortcode::shortcode($atts) . RegistrationFormShortcode::shortcode($atts);
		} else {
			$option_login_authentication_popup = Settings::getOption(Settings::OPTION_LOGIN_AUTHENTICATION_POPUP);
			if($option_login_authentication_popup == '1') {
				$content = LoginFormShortcode::shortcode($atts);
			} else if($option_login_authentication_popup == '2') {
				$content = RegistrationFormShortcode::shortcode($atts);
			} else {
				$content = LoginFormShortcode::shortcode($atts) . RegistrationFormShortcode::shortcode($atts);
			}
		}
		
		return self::loadFrontendView('overlay', compact('content', 'atts'));
	}
	
	static function getLoginButton($loginButtonText, $atts, $extraClass = '') {
		if (is_user_logged_in()) {
			$loginButtonText = Labels::getLocalized('logout_button');
			$href = wp_logout_url();
		} else {
			if (empty($loginButtonText)) {
				$loginButtonText = Labels::getLocalized('login_button');
			}
			if (isset($atts['href'])) {
				$href = $atts['href'];
			} else {
				$href = '#';
			}
		}
		return self::loadFrontendView('login-button', compact('loginButtonText', 'atts', 'href', 'extraClass'));
	}
	
	static function getLogoutButton($logoutButtonText, $atts, $extraClass = '') {
		if (!is_user_logged_in()) return;
		$logoutButtonText= Labels::getLocalized('logout_button');
		$href = wp_logout_url();
		// We're using the same view as for the login button
		$loginButtonText = $logoutButtonText;
		return self::loadFrontendView('login-button', compact('loginButtonText', 'atts', 'href', 'extraClass'));
	}

	static function getDeleteButton($deleteButtonText, $atts, $extraClass = '') {
		if (!is_user_logged_in()) return;
		if($deleteButtonText == '') {
			$deleteButtonText = Labels::getLocalized('delete_button');
		}
		$deleteButtonConfirmText = Labels::getLocalized('delete_button_confirm');
		return self::loadFrontendView('delete-button', compact('deleteButtonText', 'deleteButtonConfirmText', 'atts', 'extraClass'));
	}
	
	static function cmreg_login_overlay() {
		echo self::getOverlayView($_POST);
		exit;
	}
	
}