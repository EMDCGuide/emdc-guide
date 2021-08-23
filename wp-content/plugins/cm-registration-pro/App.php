<?php
namespace com\cminds\registration;

use com\cminds\registration\core\Core;
use com\cminds\registration\controller\SettingsController;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\ProfileField;

require_once dirname(__FILE__) . '/core/Core.php';

class App extends Core {
	
	const VERSION = '3.2.6';
	const PREFIX = 'cmreg';
	const SLUG = 'cm-registration';
	const PLUGIN_NAME = 'CM Registration';
	const PLUGIN_WEBSITE = 'https://www.cminds.com/store/cm-registration-and-invitation-codes-plugin-for-wordpress/';
	const TESTING = false;
	
	static function bootstrap($pluginFile) {
		parent::bootstrap($pluginFile);
	}
	
	static protected function getClassToBootstrap() {
		$classToBootstrap = array_merge(
			parent::getClassToBootstrap(),
			static::getClassNames('controller'),
			static::getClassNames('model'),
			static::getClassNames('metabox')
		);
		if (static::isLicenseOk()) {
			$classToBootstrap = array_merge($classToBootstrap, static::getClassNames('shortcode'), static::getClassNames('widget'));
		}
		return $classToBootstrap;
	}
	
	static function init() {
		parent::init();
		
		if (Settings::getOption(Settings::OPTION_HIDE_ADMIN_BAR)) {
			$hide_admin_bar_role_exclude_array = Settings::getOption(Settings::OPTION_HIDE_ADMIN_BAR_ROLE_EXCLUDE);
			if(count($hide_admin_bar_role_exclude_array) > 0) {
				$user = wp_get_current_user();
				if(count($user->roles) > 0) {
					if (!in_array($user->roles[0], $hide_admin_bar_role_exclude_array)) {
						add_filter('show_admin_bar', '__return_false');
					}
				}
			} else {
				add_filter('show_admin_bar', '__return_false');
			}
		}
		
		add_action('add_meta_boxes', array( __CLASS__ , 'cmreg_register_meta_boxes'));

		add_filter('send_password_change_email', array( __CLASS__ , 'send_password_change_email'), 10, 3);
		add_filter('retrieve_password_message', array( __CLASS__ , 'retrieve_password_message'), 10, 4);

		if (Settings::getOption(Settings::OPTION_LOGIN_LOG_LAST_LOGIN_DATE)) {
			add_action('wp_login', array( __CLASS__ , 'cmreg_user_last_login_date'), 10, 2);
		}
		
		if(is_user_logged_in() && !empty($_GET['cmreg-delete-account'])) {
			add_action('init', array( __CLASS__ , 'cmreg_remove_logged_in_user'), 10, 2);
		}

		if(is_user_logged_in() && !empty($_GET['cmreg-cancel-email'])) {
			add_action('init', array( __CLASS__ , 'cmreg_cancel_email'), 10, 2);
		}

		if(!empty($_GET['cmreg-confirm-email-key'])) {
			add_action('init', array( __CLASS__ , 'cmreg_confirm_email_key'), 10, 2);
		}

		wp_register_script('cmreg-utils', static::url('asset/js/utils.js'), array('jquery'), static::VERSION, true);
		wp_localize_script('cmreg-utils', 'CMREG_FrontendUtilsFieldsSettings', array(
			'toastMessageTime' => Settings::getOption(Settings::OPTION_TOAST_MESSAGE_TIME),
		));
		
		wp_register_script('cmreg-backend', static::url('asset/js/backend.js'), array('jquery'), static::VERSION, true);
		wp_register_script('cmreg-recaptcha', 'https://www.recaptcha.net/recaptcha/api.js', array(), static::VERSION, true);
		wp_register_script('cmreg_show_toast_message', static::url('asset/js/show-toast-message.js'), array('jquery', 'cmreg-utils'), static::VERSION, true);
		wp_register_script('cmreg-logout', static::url('asset/js/logout.js'), array('jquery', 'heartbeat'), static::VERSION, true);
		wp_register_script('cmreg-profile-edit', static::url('asset/js/profile-edit.js'), array('jquery', 'cmreg-utils'), static::VERSION, true);
		wp_register_script('cmreg-create-invitation-code', static::url('asset/js/create-invitation-code.js'), array('jquery', 'cmreg-utils'), static::VERSION, true);
		wp_register_script('cmreg-form-builder', static::url('asset/vendors/form-builder/form-builder.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), static::VERSION, true);
		wp_register_script('cmreg-form-builder-render', static::url('asset/vendors/form-builder/form-render.min.js'), array('jquery', 'jquery-ui-core'), static::VERSION, true);
		wp_register_script('cmreg-backend-profile-fields', static::url('asset/js/backend-profile-fields.js'), array('cmreg-form-builder'), static::VERSION, true);
		wp_register_script('cmreg-social-login-invitation-code', static::url('asset/js/social-login-invitation-code.js'), array('cmreg-utils'), static::VERSION, true);
		
		wp_register_style('cmreg-settings', static::url('asset/css/settings.css'), null, static::VERSION);
		wp_register_style('cmreg-backend', static::url('asset/css/backend.css'), null, static::VERSION);
		wp_register_style('cmreg-frontend', static::url('asset/css/frontend.css'), array('dashicons'), static::VERSION);
		wp_register_style('cmreg-form-builder', static::url('asset/vendors/form-builder/form-builder.min.css'), array(), static::VERSION);
		
		wp_register_script('cmreg-frontend', static::url('asset/js/frontend.js'), array('jquery', 'cmreg-utils', 'cmreg-form-builder-render'), static::VERSION, false);
		wp_localize_script('cmreg-frontend', 'CMREG_FrontendFieldsSettings', array(
			'toastMessageTimeForRegister' => Settings::getOption(Settings::OPTION_TOAST_MESSAGE_TIME_FOR_REGISTER),
		));
		
		wp_localize_script('cmreg-backend-profile-fields', 'CMREG_BackendProfileFieldsSettings', array(
			'registrationFormRoles' => ProfileField::getRegistrationFormRolesList(),
		));
		
	}

	static function send_password_change_email($send, $user, $userdata) {
		if($user) {
			delete_user_meta($user->ID, 'salt');
		}
	}

	static function retrieve_password_message($message, $key, $user_login, $user_data) {
		if($message) {
			if($user_data) {
				delete_user_meta($user_data->ID, 'salt');
			}
		}
	}

	static function cmreg_user_last_login_date($user_login, $user) {
		update_user_meta($user->ID, 'last_login', time());
	}

	static function cmreg_remove_logged_in_user() {
		$current_user = wp_get_current_user();
		wp_delete_user( $current_user->ID );
		wp_redirect(home_url('/'));
		exit;
	}

	static function cmreg_confirm_email_key() {
		if(isset($_GET['cmreg-confirm-email-key'])) {
			wp_enqueue_script('cmreg_show_toast_message');
			$cmreg_email_comfirm_user = get_users(array(
				"meta_key" => "cmreg_email_comfirm_key",
				"meta_value" => $_GET['cmreg-confirm-email-key'],
				"fields" => "ID"
			));
			if(count($cmreg_email_comfirm_user) > 0) {
				if(isset($cmreg_email_comfirm_user[0])) {
					$update_email_args = array(
						'ID' => $cmreg_email_comfirm_user[0],
						'user_email' => get_user_meta($cmreg_email_comfirm_user[0], 'cmreg_email_comfirm', true)
					);
					$msg = Labels::getLocalized('user_profile_email_confirm_success');
					wp_update_user($update_email_args);
					delete_user_meta($cmreg_email_comfirm_user[0], 'cmreg_email_comfirm');
					delete_user_meta($cmreg_email_comfirm_user[0], 'cmreg_email_comfirm_key');
				}
				else {
					$msg = Labels::getLocalized('user_profile_email_confirm_failure');
				}
			} else {
				$msg = Labels::getLocalized('user_profile_email_confirm_failure');
			}
			add_action('wp_enqueue_scripts', function() use ($msg) {
				wp_localize_script('cmreg_show_toast_message', 'cmreg_show_toast_message', compact('msg'));
			});
		}
	}

	static function cmreg_cancel_email() {
		$current_user = wp_get_current_user();
		delete_user_meta($current_user->ID, 'cmreg_email_comfirm');
		delete_user_meta($current_user->ID, 'cmreg_email_comfirm_key');
	}
	
	static function cmreg_register_meta_boxes() {
		add_meta_box('meta-box-cmrreg-login-box', 'CM Registration Authentication', array( __CLASS__ , 'cmreg_register_meta_boxes_callback' ), '', 'side', 'high');
	}

	static function cmreg_register_meta_boxes_callback() {
		global $post;
		$cmreg_login_access = get_post_meta($post->ID, 'cmreg_login_access', true);
		$outout = 'Show pop-up window if a user is not logged in?<br />';
		$outout .= '<div style="margin:10px 0;">';
		if($cmreg_login_access == '') {
			$outout .= '<input type="radio" name="cmreg_login_access" value="1" /> Yes<br />';
			$outout .= '<input type="radio" name="cmreg_login_access" value="0" checked="checked" /> No<br />';
		} else {
			if($cmreg_login_access == '1') {
				$outout .= '<input type="radio" name="cmreg_login_access" value="1" checked="checked" /> Yes<br />';
			} else {
				$outout .= '<input type="radio" name="cmreg_login_access" value="1" /> Yes<br />';
			}
			if($cmreg_login_access == '0') {
				$outout .= '<input type="radio" name="cmreg_login_access" value="0" checked="checked" /> No<br />';
			} else {
				$outout .= '<input type="radio" name="cmreg_login_access" value="0" /> No<br />';
			}
		}
		$outout .= '</div>';
		echo $outout;
	}

	static function admin_menu() {
		parent::admin_menu();
		$name = static::getPluginName(true);
		//$page = add_menu_page($name, $name, 'manage_options', static::PREFIX, array(App::namespaced('controller\SettingsController'), 'render'), 'dashicons-admin-users', 5679);
		if (App::isPro()) {
			add_menu_page($name, $name, 'manage_options', static::SLUG); //, array(App::namespaced('controller\SettingsController'), 'render'));
		} else {
			add_menu_page($name, $name, 'manage_options', static::SLUG, array(App::namespaced('controller\SettingsController'), 'render'));
		}
	}
	
}