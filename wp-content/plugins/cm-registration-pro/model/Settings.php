<?php
namespace com\cminds\registration\model;

use com\cminds\registration\lib\Email;
use com\cminds\registration\App;

class Settings extends SettingsAbstract {

	public static $categories = array(
		'general' => 'General',
		'login' => 'Login',
		'register' => 'Registration',
		//'invitations' => 'Invitations',
		//'fields' => 'Custom fields',
	);
	
	public static $subcategories = array(
		'general' => array(
			'general' => 'General',
			'api' => 'API Keys',
			'editprofileform' => 'Edit Profile Form',		
			'logout' => 'Logout',
			'appearance' => 'Appearance',
			'dashboard' => 'Dashboard',
			'toastmessage' => 'Toast Message',
			'adminbar' => 'Admin Bar',
			'selfregister' => 'Self Register',
		),
		'login' => array(
			'access' => 'Access Restriction Content',
			'login' => 'Login',
			'social-login' => 'Social Login',
			'limit' => 'Limit login attempts',
			'ip' => 'IP restrictions',
			'redirect_role' => 'Redirection per role',
		),
		'register' => array(
			'register' => 'Registration',
			'pass' => 'Password',
			'verification' => 'Email verification',
			'ip' => 'IP restrictions',
			's2member' => 'S2Member Pro integration',
			'gravity_forms' => 'Gravity Forms integration',
			'age_verification' => 'Age verification',
		),
		'invitations' => array(
			'invitations' => 'Invitations settings',
			'email' => 'Email template',
			'dashboard' => 'Dashboard',
			'edit_profile' => 'Edit profile',
		),
		/*
		'fields' => array(
 			'fields' => 'Custom fields',
 		),
		*/
		'email' => array(
			//'general' => 'General email settings',
			'password_email' => 'Password email',
			'welcome' => 'Welcome email',
			'activation' => 'Activation email',
			'admin' => 'Administrator notifications',
			'invite_friend' => 'Invite friend',
			'account_deleted' => 'Account deleted',
			'reset_password_email' => 'Reset Password email',
			'self_register_email' => 'Self register email',
			'edit_profile_confirm_email' => 'Edit profile confirm email',
		),
		'custom_css'    => array(
            'custom_css' => 'Custom CSS',
        ),
		'labels' => array(
			'other' => 'Other',
		),
	);

	const TYPE_LIST_KEY_VALUE = 'list_key_value';
	const OPTION_HIDE_ADMIN_BAR = 'cmreg_hide_admin_bar';
	const OPTION_HIDE_ADMIN_BAR_ROLE_EXCLUDE = 'cmreg_hide_admin_bar_role_exclude';

	const OPTION_HIDE_DISPLAY_NAME_FIELD = 'cmreg_hide_display_name_field';
	const OPTION_HIDE_WEBSITE_URL_FIELD = 'cmreg_hide_website_url_field';
	const OPTION_HIDE_ABOUT_ME_FIELD = 'cmreg_hide_about_me_field';
	const OPTION_EMAIL_CONFIRM_ENABLE = 'cmreg_email_confirm_enable';

	const OPTION_CUSTOM_CSS = 'cmreg_custom_css';
	const OPTION_TOAST_MESSAGE_TIME = 'cmreg_toast_message_time';
	const OPTION_LOGIN_FIELD = 'cmreg_login_field';
	const OPTION_WP_LOGIN_PAGE_REDIRECTION_URL = 'cmreg_wp_login_page_redirection_url';
	const OPTION_WP_LOSTPASSWORD_PAGE_REDIRECTION_URL = 'cmreg_wp_lostpassword_page_redirection_url';

	const OPTION_LOGIN_AUTHENTICATION_POPUP = 'cmreg_login_authentication_popup';
	const OPTION_LOGIN_AUTHENTICATION_POPUP_FORCE = 'cmreg_login_authentication_popup_force';
	const OPTION_PASSWORD_HIDE_CHARS = 'cmreg_password_hide_chars';

	const OPTION_LOGIN_GLOBAL_SITE_ACCESS = 'cmreg_login_global_site_access';
	const OPTION_LOGIN_CUSTOM_REDIRECT_URL = 'cmreg_login_custom_redirect_url';
	const OPTION_LOGIN_EXCLUDE_REDIRECT_URL = 'cmreg_login_exclude_redirect_url';

	const OPTION_LOGIN_REDIRECT_URL = 'cmreg_login_redirect_url';
	const OPTION_REGISTER_REDIRECT_URL = 'cmreg_register_redirect_url';
	const OPTION_LOGIN_REMEMBER_ENABLE = 'cmreg_login_remember_enable';
	const OPTION_LOGIN_LOG_LAST_LOGIN_DATE = 'cmreg_login_log_last_login_date';
	const OPTION_LOGIN_LOST_PASSWORD_ENABLE = 'cmreg_login_lost_password_enable';
	const OPTION_RECAPTCHA_API_SITE_KEY = 'cmreg_recaptcha_api_site_key';
	const OPTION_RECAPTCHA_API_SECRET_KEY = 'cmreg_recaptcha_api_secret_key';
	const OPTION_LOGOUT_REDIRECT_URL = 'cmreg_logout_redirect_url';
	const OPTION_REGISTER_DISPLAY_NAME_ENABLE = 'cmreg_register_display_name_enable';
	const OPTION_REGISTER_DEFAULT_ROLE = 'cmreg_register_default_role';
	const OPTION_REGISTER_MINIMUM_AGE = 'cmreg_register_minimum_age';
	const OPTION_REGISTER_BIRTH_DATE_FIELD_META_KEY = 'cmreg_register_birth_date_field_meta_key';
	const OPTION_REGISTER_EMAIL_ENABLE = 'cmreg_register_email_enable';
	const OPTION_REGISTER_REPEAT_EMAIL_ENABLE = 'cmreg_register_repeat_email_enable';
	const OPTION_REGISTER_PASS_ENABLE = 'cmreg_register_pass_enable';
	const OPTION_REGISTER_REPEAT_PASS_ENABLE = 'cmreg_register_repeat_pass_enable';
	const OPTION_S2MEMBERS_ENABLE = 'cmreg_s2members_enable';
	const OPTION_REGISTER_INVIT_CODE = 'cmreg_register_invit_code_require';
	const OPTION_REGISTER_RECAPTCHA_ENABLE = 'cmreg_register_recaptcha_enable';
	const OPTION_REGISTER_LOGIN_ENABLE = 'cmreg_register_login_enable';
	const OPTION_REGISTER_ORGANIZATION_ENABLE = 'cmreg_register_organization_enable';
	const OPTION_REGISTER_PRVENT_SYSTEM_EMAIL = 'cmreg_register_prevent_system_email';
	//const OPTION_GRAVITY_FORMS_REGISTRATION_HOOK_ENABLE = 'cmreg_gravity_forms_reg_hook_enable';
	const OPTION_WP_REGISTER_PAGE_REDIRECTION_URL = 'cmreg_wp_register_page_redirection_url';
	const OPTION_TOAST_MESSAGE_TIME_FOR_REGISTER = 'cmreg_cmreg_toast_message_time_for_register';
	const OPTION_LOGIN_RECAPTCHA_ENABLE = 'cmreg_login_recaptcha_enable';
	const OPTION_LOGIN_TERMS_OF_SERVICE_CHECKBOX_TEXT = 'cmreg_login_terms_of_service_checkbox_text';
	const OPTION_LOGIN_LIMIT_ATTEMPTS_ACTION = 'cmreg_login_limit_attempts_action';
	const OPTION_LOGIN_LIMIT_ATTEMPTS_NUMBER = 'cmreg_login_limit_attempts_number';
	const OPTION_LOGIN_LIMIT_ATTEMPTS_INTERVAL_MINUTES = 'cmreg_login_limit_attempts_interval_minutes';
	const OPTION_LOGIN_LIMIT_ATTEMPTS_SEND_USER_EMAIL = 'cmreg_login_limit_attempts_send_user_email';
	const OPTION_PREVENT_CALLING_LOGIN_FOOTER_FRONTEND = 'cmreg_prevent_calling_login_footer_frontend';
	const OPTION_REGISTER_S2MEMBER_DEFAULT_LEVEL = 'cmreg_register_s2member_default_level';
	const OPTION_REGISTER_WELCOME_EMAIL_ENABLE = 'cmreg_register_welcome_email_enable';
	const OPTION_REGISTER_WELCOME_EMAIL_SUBJECT = 'cmreg_register_welcome_email_subject';
	const OPTION_REGISTER_WELCOME_EMAIL_BODY = 'cmreg_register_welcome_email_body';
	const OPTION_REGISTER_ACTIVATION_EMAIL_SUBJECT = 'cmreg_register_activation_email_subject';
	const OPTION_REGISTER_ACTIVATION_EMAIL_BODY = 'cmreg_register_activation_email_body';
	const OPTION_ACCOUNT_DELETED_USER_EMAIL_ENABLE = 'cmreg_account_deleted_user_email_enable';
	const OPTION_ACCOUNT_DELETED_USER_EMAIL_SUBJECT = 'cmreg_account_deleted_user_email_subject';
	const OPTION_ACCOUNT_DELETED_USER_EMAIL_BODY = 'cmreg_account_deleted_user_email_body';
	const OPTION_PASSWORD_EMAIL_SUBJECT = 'cmreg_password_email_subject';
	const OPTION_PASSWORD_EMAIL_BODY = 'cmreg_password_email_body';
	const OPTION_RESET_PASSWORD_EMAIL_SUBJECT = 'cmreg_reset_password_email_subject';
	const OPTION_RESET_PASSWORD_EMAIL_BODY = 'cmreg_reset_password_email_body';
	const OPTION_SELF_REGISTER_EMAIL_SUBJECT = 'cmreg_self_register_email_subject';
	const OPTION_SELF_REGISTER_EMAIL_BODY = 'cmreg_self_register_email_body';
	const OPTION_EDIT_PROFILE_CONFIRM_EMAIL_SUBJECT = 'cmreg_edit_profile_confirm_email_subject';
	const OPTION_EDIT_PROFILE_CONFIRM_EMAIL_BODY = 'cmreg_edit_profile_confirm_email_body';
	const OPTION_REGISTER_EMAIL_VERIFICATION_ON_SECONDARY_EMAIL_ENABLE = 'cmreg_register_email_verification_on_secondary_email_enable';
	const OPTION_REGISTER_SECONDARY_EMAIL_FIELD_META_KEY = 'cmreg_register_secondary_email_field_meta_key';
	const OPTION_REGISTER_EMAIL_VERIFICATION_ENABLE = 'cmreg_register_email_verification_enable';
	const OPTION_REGISTER_EMAIL_VERIFICATION_AUTOLOGIN = 'cmreg_register_email_verification_autologin';
	const OPTION_REGISTER_ADMIN_NOTIFY_EMAIL = 'cmreg_register_admin_notify_email';
	const OPTION_REGISTER_ADMIN_NOTIFY_REGISTERED = 'cmreg_register_admin_notify_registered';
	const OPTION_REGISTER_ADMIN_NOTIFY_ACTIVATED = 'cmreg_register_admin_notify_activated';
	const OPTION_REGISTER_WELCOME_PAGE = 'cmreg_register_welcome_page';
	const OPTION_REGISTER_STRONG_PASS_ENABLE = 'cmreg_register_strong_pass_enable';
	const OPTION_REGISTER_DAYS_FOR_VERIFICATION = 'cmreg_register_days_for_verification';
	const OPTION_OVERLAY_OPACITY = 'cmreg_overlay_opacity';
	const OPTION_OVERLAY_PRELOAD = 'cmreg_overlay_preload';
	const OPTION_FORM_FIELD_LABEL_ENABLE = 'cmreg_form_field_label_enable';
	const OPTION_LOGOUT_INACTIVITY_TIME = 'cmreg_logout_inactivity_time_min';
	const OPTION_RELOAD_AFTER_LOGOUT = 'cmreg_reload_after_logout';
	const OPTION_REGISTER_NOTICE_ADMIN_ENABLE = 'cmreg_register_notice_admin_enable';
	const OPTION_REGISTER_EXTRA_FIELDS = 'cmreg_register_extra_fields';
	const OPTION_TERMS_OF_SERVICE_CHECKBOX_TEXT = 'cmreg_toc_checkbox_text';
	const OPTION_AFTER_REGISTER_AUTOLOGIN_ENABLE = 'cmreg_after_register_autologin_enable';
	const OPTION_EMAIL_USE_HTML = 'cmreg_email_use_html';
	const OPTION_SOCIAL_LOGIN_FACEBOOK_APP_ID = 'cmreg_social_login_facebook_app_id';
	const OPTION_SOCIAL_LOGIN_FACEBOOK_APP_SECRET = 'cmreg_social_login_facebook_app_secret';
	const OPTION_SOCIAL_LOGIN_GOOGLE_APP_ID = 'cmreg_social_login_google_app_id';
	const OPTION_SOCIAL_LOGIN_GOOGLE_APP_SECRET = 'cmreg_social_login_google_app_secret';
	const OPTION_SOCIAL_LOGIN_ENABLE = 'cmreg_social_login_enable';
	const OPTION_LOGIN_SHOW_SOCIAL_LOGIN_BUTTONS = 'cmreg_login_show_social_login_buttons';
	const OPTION_REGISTER_SHOW_SOCIAL_LOGIN_BUTTONS = 'cmreg_register_show_social_login_buttons';
	const OPTION_SOCIAL_LOGIN_ENABLE_ALLOW_REGISTRATION = 'cmreg_social_login_allow_registration';
	const OPTION_SOCIAL_LOGIN_ASK_INVITATION_CODE = 'cmreg_social_login_ask_invitation_code';
	const OPTION_REGISTER_IP_ALLOW = 'cmreg_register_ip_allow';
	const OPTION_REGISTER_IP_DENY = 'cmreg_register_ip_deny';
	const OPTION_LOGIN_IP_ALLOW = 'cmreg_login_ip_allow';
	const OPTION_LOGIN_IP_DENY= 'cmreg_login_ip_deny';
	const OPTION_INVITE_FRIEND_EMAIL_SUBJECT = 'cmreg_invite_friend_email_subject';
	const OPTION_INVITE_FRIEND_EMAIL_BODY = 'cmreg_invite_friend_email_body';
	const OPTION_INVITE_FRIEND_REGISTRATION_PAGE_URL = 'cmreg_invite_friend_registration_page_url';
	const OPTION_INVITE_FRIEND_LIMIT_PER_USER = 'cmreg_invite_friend_limit_per_user';
	const OPTION_INVITE_AUTO_POPUP_ENABLE = 'cmreg_invite_auto_popup_enable';
	const OPTION_DASHBOARD_USERS_COLUMN_INVIT_CODE_ENABLE = 'cmreg_dashboard_users_column_invit_code_enable';
	const OPTION_USER_PROFILE_INVIT_CODE_SHOW = 'cmreg_user_profile_invit_code_show';
	const OPTION_LOGIN_REDIRECTION_PER_ROLE = 'cmreg_login_redirection_per_role';
	
	const OPTION_SELF_REGISTER_ENABLE = 'cmreg_self_register_enable';
	const OPTION_SELF_REGISTER_SHORT_PERIOD_INTERVAL = 'cmreg_self_register_short_period_interval';
	const OPTION_SELF_REGISTER_LONG_PERIOD_INTERVAL = 'cmreg_self_register_long_period_interval';
	const OPTION_SELF_REGISTER_LONG_PERIOD_EMAIL_ENABLE = 'cmreg_self_register_long_period_email_enable';
	const OPTION_SELF_REGISTER_API_URL = 'cmreg_self_register_api_url';

	const LOGIN_FIELD_EMAIL = 'email';
	const LOGIN_FIELD_LOGIN = 'login';
	const LOGIN_FIELD_BOTH = 'both';
	
	const INVITATION_CODE_DISABLED = 'disabled';
	const INVITATION_CODE_OPTIONAL = 'optional';
	const INVITATION_CODE_REQUIRED = 'required';
	
	const LIMIT_ATTEMPTS_ACTION_DISABLED = 'disabled';
	const LIMIT_ATTEMPTS_ACTION_SHOW_CAPTCHA = 'show_captcha';
	const LIMIT_ATTEMPTS_ACTION_WAIT = 'wait';
	
	const STRONG_PASSWORD_REGEXP = '~^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$~';
	
	public static function getOptionsConfig() {
		
		return apply_filters('cmreg_options_config', array(
			Settings::OPTION_LOGIN_AUTHENTICATION_POPUP => array(
				'type' => Settings::TYPE_SELECT,
				'default' => 0,
				'options' => array('0'=>'Login & Register', '1'=>'Only Login', '2'=>'Only Registration'),
				'category' => 'general',
				'subcategory' => 'general',
				'title' => 'Authentication pop-up window',
				'desc' => 'What forms will the pop-up contain. You can activate or deactivate the pop-up from each page/post',
			),
			self::OPTION_LOGIN_AUTHENTICATION_POPUP_FORCE => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 0,
				'category' => 'general',
				'subcategory' => 'general',
				'title' => 'Allow users to close the pop-up',
				'desc' => 'If enabled, the user will able to close the pop-up.',
			),
			self::OPTION_PASSWORD_HIDE_CHARS => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 1,
				'category' => 'general',
				'subcategory' => 'general',
				'title' => 'Hide password field characters',
				'desc' => 'If enabled then the password field characters typed on the frontend will remain hidden until the user clicks the eye icon (<span class="dashicons dashicons-hidden"></span>). Otherwise, typed characters will be visible all the times.',
			),
			Settings::OPTION_LOGIN_GLOBAL_SITE_ACCESS => array(
				'type' => Settings::TYPE_SELECT,
				'default' => 0,
				'options' => array('0'=>'Site accessible to Everyone', '1'=>'Site accessible to Logged In Users'),
				'category' => 'login',
				'subcategory' => 'access',
				'title' => 'Global Site Access',
				'desc' => 'Control the global access to your site.',
			),
			self::OPTION_LOGIN_CUSTOM_REDIRECT_URL => array(
				'type' => self::TYPE_STRING,
				'category' => 'login',
				'subcategory' => 'access',
				'title' => 'Custom Redirect URL',
				'desc' => 'Logged out users will be redirected to this URL if they are not permitted to access the site. You can use URLs such as http://localhost/knowledgetrail/login. If this field is left empty, users will be redirected to your home page by default.',
			),
			self::OPTION_LOGIN_EXCLUDE_REDIRECT_URL => array(
				'type' => self::TYPE_TEXTAREA,
				'default' => '',
				'category' => 'login',
				'subcategory' => 'access',
				'title' => 'Exclude the following URLs',
				'desc' => 'Separate URL by new lines.<br>Examples:<br><code>'.site_url().'/login</code><br><code>'.site_url().'/register</code>',
			),
			self::OPTION_LOGIN_REDIRECT_URL => array(
				'type' => self::TYPE_STRING,
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Redirect after login to URL address',
				'desc' => 'Enter an optional URL where users will be redirected after login. If this field is left empty, users will stay on the same page.<br />'
							. 'You can use the <kbd>%usernicename%</kbd>, <kbd>%userlogin%</kbd> and <kbd>%usersitename%</kbd> parameters in the URL.<br><br>For example: '
							. '<kbd>/welcome/%usernicename%</kbd>.',
			),
			self::OPTION_LOGIN_REMEMBER_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Enable the "Remember me" option',
			),
			self::OPTION_LOGIN_LOG_LAST_LOGIN_DATE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Log user last login date',
				'desc' => 'You can use <kbd>[cmreg_login_date id="user-id"]</kbd> to show users their last login date on the frontend.',
			),
			self::OPTION_LOGIN_LOST_PASSWORD_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Enable lost password form',
			),
			self::OPTION_LOGIN_FIELD => array(
				'type' => self::TYPE_RADIO,
				'default' => static::LOGIN_FIELD_BOTH,
				'options' => array(
					self::LOGIN_FIELD_EMAIL => 'email',
					self::LOGIN_FIELD_LOGIN => 'login',
					self::LOGIN_FIELD_BOTH => 'both email or login',
				),
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Login using field',
			),
			self::OPTION_WP_LOGIN_PAGE_REDIRECTION_URL => array(
				'type' => self::TYPE_STRING,
				'default' => '',
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Disable wp-login.php and login page redirect to this URL',
				'desc' => 'You can disable the regular WordPress login page (wp-login.php) and redirect users to this specified URL address where they can find the CM Registration login form/shortcode. This will also affect the lost password page. Leave blank to enable the wp-login.php page.',
			),
			self::OPTION_WP_LOSTPASSWORD_PAGE_REDIRECTION_URL => array(
				'type' => self::TYPE_STRING,
				'default' => '',
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Disable wp-login.php and lost password page redirect to this URL',
				'desc' => 'You can disable the regular WordPress lost password page (wp-login.php) and redirect users to this specified URL address where they can find the CM Registration reset password shortcode <code>[cmreg-reset-password]</code>. Leave blank to enable the wp-login.php page.',
			),
			self::OPTION_PREVENT_CALLING_LOGIN_FOOTER_FRONTEND => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'login',
				'subcategory' => 'login',
				'title' => 'Prevent from calling login_footer action in the front-end',
				'desc' => 'Enabling this option can resolve some issues with the login form on the front end by preventing calling the login_footer action. For example, it solves the known problem of login forms not working when you\'re using the NextGEN Gallery plugin.'
			),
			self::OPTION_LOGOUT_REDIRECT_URL => array(
				'type' => self::TYPE_STRING,
				'category' => 'general',
				'subcategory' => 'logout',
				'title' => 'Redirect after logout to URL address',
				'desc' => 'You can enter a custom URL address that users will be redirected after logout.',
			),
			self::OPTION_REGISTER_LOGIN_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Allow user to enter his login',
				'desc' => 'If disabled, the login will be created from the entered email address. The login is need during the singing-in.',
			),
			self::OPTION_REGISTER_DISPLAY_NAME_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Allow user to enter his publicly displayed name',
				'desc' => 'If disabled, the public name will be his email address. If enabled user can enter name that will be displayed next to '
							. 'his comments or posts.',
			),
			self::OPTION_REGISTER_ORGANIZATION_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Allow user to add organization',
				'desc' => 'If enabled, then organization field will be show during the registration.',
			),
			self::OPTION_REGISTER_EMAIL_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Require to email',
				'desc' => 'If enabled, then email field will be required during the registration.',
			),
			self::OPTION_REGISTER_REPEAT_EMAIL_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Require to repeat email',
				'desc' => 'If enabled, then repeat email field will be required during the registration.',
			),
			self::OPTION_REGISTER_NOTICE_ADMIN_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Notify admin about new registration',
				'desc' => 'If enabled then the default notification email will be send to: '. get_bloginfo('admin_email'),
			),
			self::OPTION_REGISTER_PRVENT_SYSTEM_EMAIL => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Prevent sending the standard WP welcome email',
				'desc' => 'If enabled the regular Wordpress\' welcome mail won\'t be send to the user. For some specific cases you may need to disable this option.',
			),
			self::OPTION_WP_REGISTER_PAGE_REDIRECTION_URL => array(
				'type' => self::TYPE_STRING,
				'default' => '',
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Disable WP registration page and redirect to this URL',
				'desc' => 'You can disable the regular Wordpress registration page (wp-login.php?action=register) and redirect users to the specified URL address '
						. 'where they can find the CM Registration form/shortcode. Leave blank to enable the wp-login.php page.',
			),
			self::OPTION_TOAST_MESSAGE_TIME_FOR_REGISTER => array(
				'type' => Settings::TYPE_INT,
				'default' => 10,
				'category' => 'register',
				'subcategory' => 'register',
				'title' => 'Toast Message Time Duration [seconds]',
				'desc' => 'Toast messages are nonintrusive alerts that pop up over the content. The default value is 20 seconds.',
			),
			self::OPTION_REGISTER_PASS_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'register',
				'subcategory' => 'pass',
				'title' => 'Require to password',
			),
			self::OPTION_REGISTER_REPEAT_PASS_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'register',
				'subcategory' => 'pass',
				'title' => 'Require to repeat password',
			),
			self::OPTION_REGISTER_STRONG_PASS_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => 1,
				'category' => 'register',
				'subcategory' => 'pass',
				'title' => 'Require strong passwords',
				'desc' => 'Password must be at least 8 characters long and must contain at least one lowercase letter, one uppercase letter and one digit.<br />'
							.'Regular expression: <kbd>'. Settings::STRONG_PASSWORD_REGEXP .'</kbd>',
			),
			/*
 			self::OPTION_REGISTER_ADMIN_NOTIFY_EMAIL => array(
 				'type' => self::TYPE_CSV_LINE,
 				'default' => function() { return array(get_bloginfo('admin_email')); },
 				'category' => 'email',
 				'subcategory' => 'admin',
 				'title' => 'Administrators emails for notifications',
 				'desc' => 'Enter comma separated email addresses.',
 			),
 			self::OPTION_REGISTER_ADMIN_NOTIFY_REGISTERED => array(
 				'type' => self::TYPE_BOOL,
 				'default' => 0,
 				'category' => 'email',
 				'subcategory' => 'admin',
 				'title' => 'Notify administrator about new registration',
 				'desc' => 'Send email when user has registered his email account (even if not confirmed yet).',
 			),
 			self::OPTION_REGISTER_ADMIN_NOTIFY_ACTIVATED => array(
 				'type' => self::TYPE_BOOL,
 				'default' => 0,
 				'category' => 'email',
 				'subcategory' => 'admin',
 				'title' => 'Notify administrator about account activation',
 				'desc' => 'Send email when user has confirmed his email address (works only when email confirmation is required).',
 			),
			*/
			self::OPTION_CUSTOM_CSS => array(
				'type' => self::TYPE_TEXTAREA,
				'default' => '',
				'category' => 'custom_css',
				'subcategory' => 'custom_css',
				'title' => 'Custom CSS',
				'desc' => 'You can enter a custom CSS which will be embeded on every page that contains a CM Registration interface.',
			),

			self::OPTION_TOAST_MESSAGE_TIME => array(
				'type' => Settings::TYPE_INT,
				'default' => 10,
				'category' => 'general',
				'subcategory' => 'toastmessage',
				'title' => 'Toast Message Time Duration [seconds]',
				'desc' => 'Toast messages are nonintrusive alerts that pop up over the content. The default value is 10 seconds.',
			),
			self::OPTION_HIDE_ADMIN_BAR => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 0,
				'category' => 'general',
				'subcategory' => 'adminbar',
				'title' => 'Hide admin bar',
				'desc' => 'If enabled, admin top bar hide for all on frontend.',
			),
			self::OPTION_HIDE_ADMIN_BAR_ROLE_EXCLUDE => array(
				'type' => self::TYPE_MULTICHECKBOX,
				'default' => '',
				'options' => Settings::getRolesOptions(),
				'category' => 'general',
				'subcategory' => 'adminbar',
				'title' => 'Allow admin bar for specific roles',
				'desc' => 'If above option is enabled and you want to allow admin bar for specific roles please select here.',
			),
			self::OPTION_SELF_REGISTER_ENABLE => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 0,
				'category' => 'general',
				'subcategory' => 'selfregister',
				'title' => 'Enable self register',
				'desc' => 'If enabled, then self register link will display below forgot your password link.',
			),
			self::OPTION_SELF_REGISTER_SHORT_PERIOD_INTERVAL => array(
				'type' => Settings::TYPE_INT,
				'default' => 5,
				'category' => 'general',
				'subcategory' => 'selfregister',
				'title' => 'Short period message interval',
				'desc' => 'You can manage message text on labels tab and default value is 5.',
			),
			self::OPTION_SELF_REGISTER_LONG_PERIOD_INTERVAL => array(
				'type' => Settings::TYPE_INT,
				'default' => 100,
				'category' => 'general',
				'subcategory' => 'selfregister',
				'title' => 'Long period message interval',
				'desc' => 'You can manage message text on labels tab and default value is 100.',
			),
			self::OPTION_SELF_REGISTER_LONG_PERIOD_EMAIL_ENABLE => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 0,
				'category' => 'general',
				'subcategory' => 'selfregister',
				'title' => 'Enable email to admin',
				'desc' => 'If enabled, then email sent to site administartor if there\'s over long period interval with failed submissions from one IP.',
			),
			self::OPTION_SELF_REGISTER_API_URL => array(
				'type' => self::TYPE_STRING,
				'default' => '',
				'category' => 'general',
				'subcategory' => 'selfregister',
				'title' => 'API URL',
				'desc' => '',
			),
			self::OPTION_HIDE_DISPLAY_NAME_FIELD => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 0,
				'category' => 'general',
				'subcategory' => 'editprofileform',
				'title' => 'Hide display name field',
				'desc' => 'If enabled, display name field hide from edit profile form.',
			),
			self::OPTION_HIDE_WEBSITE_URL_FIELD => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 0,
				'category' => 'general',
				'subcategory' => 'editprofileform',
				'title' => 'Hide website url field',
				'desc' => 'If enabled, website url field hide from edit profile form.',
			),
			self::OPTION_HIDE_ABOUT_ME_FIELD => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 0,
				'category' => 'general',
				'subcategory' => 'editprofileform',
				'title' => 'Hide about me field',
				'desc' => 'If enabled, about me field hide from edit profile form.',
			),
			self::OPTION_EMAIL_CONFIRM_ENABLE => array(
				'type' => Settings::TYPE_BOOL,
				'default' => 0,
				'category' => 'general',
				'subcategory' => 'editprofileform',
				'title' => 'Email Confirm Enable',
				'desc' => 'If enabled, then user able to change email after confirm the new email address.',
			)
			
		));
		
	}
	
	static function listShortcodes($vars) {
		$out = '';
		foreach ($vars as $name => $val) {
			$out .= $name . '<br />';
		}
		return $out;
	}
	
}