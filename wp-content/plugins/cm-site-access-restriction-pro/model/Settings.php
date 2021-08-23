<?php
namespace com\cminds\siteaccessrestriction\model;

use com\cminds\siteaccessrestriction\lib\Email;
use com\cminds\siteaccessrestriction\App;

class Settings extends SettingsAbstract {

	const OPTION_ACCESS_DENIED_REDIRECT_URL = 'cmacc_access_denied_redirect_url';
	const OPTION_LOGIN_REDIRECT_URL = 'cmacc_login_redirect_url';
	const OPTION_POST_TYPES_GLOBALS = 'cmacc_post_types_globals';
	const OPTION_ACCESS_DENIED_USER_ROLES = 'cmacc_access_denied_user_roles';
	const OPTION_DAYS_ACCESS_DENIED_REDIRECT_URL = 'cmacc_days_access_denied_redirect_url';
	const OPTION_RESTRICT_ACCESS_TYPE = 'cmacc_restrict_type';
	const OPTION_RESTRICT_WITH_CUSTOM_FILTER = 'cmacc_restrict_with_custom_filter';
	const OPTION_RESTRICT_HOMEPAGE_LATEST_POSTS = 'cmacc_restrict_homepage_latest_posts';
	const OPTION_RESTRICT_MESSAGE_FOR_ARCHIVE = 'cmacc_restrict_message_for_archive';
	const OPTION_ACCESS_DENIED_BLACKLIST = 'cmacc_access_denied_blacklist';
	const OPTION_ACCESS_GRANTED_WHITELIST = 'cmacc_access_granted_whitelist';
	const OPTION_ACCESS_DENIED_LOGGED_IN_USERS = 'cmacc_access_denied_logged_in';
	const OPTION_SHORTCODE_ACCESS_DENIED_TEXT = 'cmacc_shortcode_access_denied_text';

	public static $categories = array(
		'general' => 'General',
		'post_types' => 'Post Types',
		'labels' => 'Labels',
		'shortcodes' => 'Shortcodes',
	);

	public static $subcategories = array(
		'general' => array(
			'general' => 'General',
		),
		'post_types' => array(
			'post_types' => 'Post types',
		),
	);

	public static function getOptionsConfig() {

		return apply_filters('cmacc_options_config', array(

			self::OPTION_ACCESS_DENIED_REDIRECT_URL => array(
				'type' => self::TYPE_STRING,
				'category' => 'general',
				'subcategory' => 'general',
				'title' => 'Access Denied page URL',
				'desc' => 'Enter a URL that users will be redirected to when trying to open a resource they don\'t have access to. You can use the optional parameter %backlink% which will be replaced with the target website\'s URL address.',
			),
			self::OPTION_LOGIN_REDIRECT_URL => array(
				'type' => self::TYPE_STRING,
				'category' => 'general',
				'subcategory' => 'general',
				'title' => 'Login page URL',
				'desc' => 'Enter a URL that users will be redirected to when trying to open a resource they don\'t hae acess to when not logged-in. If no URL is specified then the user will be redirected to the Access Denied page specified above. You can use the optional parameter %backlink% which will be replaced with the target website\'s URL address.',
			),
            self::OPTION_DAYS_ACCESS_DENIED_REDIRECT_URL => array(
                'type' => self::TYPE_SELECT,
                'options' => Settings::getAvailablePages(),
                'category' => 'general',
                'subcategory' => 'general',
                'title' => 'Access denied due to time restrictions',
                'desc' => 'Choose the page that the user will be redirected to when trying to open the resource outside of an approved time.',
            ),
			self::OPTION_ACCESS_DENIED_USER_ROLES => array(
				'type' => self::TYPE_MULTICHECKBOX,
				'options' => Settings::getRolesOptionsExceptAdmin(),
				'category' => 'general',
				'subcategory' => 'general',
				'title' => 'Restrict dashboard access',
				'desc' => 'Dashboard access can be restricted to users of certain roles only or users with a specific capability. Administrator user have full access',
			),
			self::OPTION_RESTRICT_ACCESS_TYPE => array(
				'type' => self::TYPE_RADIO,
				'options' => Settings::getRestrictType(),
				'category' => 'general',
				'subcategory' => 'general',
				'title' => 'Restriction type',
				'desc' => '<strong>Restrict full content:</strong> Choose this if you want to restrict full content.<br><strong>Restrict partially content:</strong> Choose this if you want to show only 20% of the content are displayed with fade effect and message.<br><strong>Restrict content with shortcode only:</strong> Choose this if you want to restrict content with [access] shortcode only. <a href="https://creativeminds.helpscoutdocs.com/article/1366-site-access-restriction-shortcodes" target="_blank">Click here</a> to read more about shortcode.',
				'default' => 'full',
			),
			self::OPTION_RESTRICT_WITH_CUSTOM_FILTER => array(
				'type' => self::TYPE_BOOL,
				'category' => 'general',
				'subcategory' => 'general',
				'title' => 'Enable restriction with custom filter',
				'desc' => 'If you want to restrict with custom filter called "<strong>cmsar_single_content</strong>" in your theme then you should enable this option.<br>e.g. <strong>apply_filters(\'cmsar_single_content\', $content);</strong></a>',
				'default' => '0',
			),
			self::OPTION_RESTRICT_HOMEPAGE_LATEST_POSTS => array(
				'type' => self::TYPE_BOOL,
				'category' => 'general',
				'subcategory' => 'general',
				'title' => 'Exclude homepage displays with your latest posts',
				'desc' => 'If enabled then home page access not restricted if "Your homepage displays" option set "Your latest posts" under <a href="options-reading.php" target="_blank">Reading Settings</a>',
				'default' => '1',
			),
			self::OPTION_RESTRICT_MESSAGE_FOR_ARCHIVE => array(
				'type' => self::TYPE_BOOL,
				'category' => 'general',
				'subcategory' => 'general',
				'title' => 'Display restriction label on archive page',
				'desc' => 'If enabled then restriction label will show under each post on archive page. You can manage label text under "Labels" tab.',
				'default' => '0',
			),
            self::OPTION_ACCESS_DENIED_BLACKLIST => array(
                'type' => self::TYPE_MULTICHECKBOX,
                'category' => '',
                'subcategory' => '',
                'title' => 'Restrict dashboard access',
                'desc' => 'Dashboard access can be restricted to users from blacklist.',
            ),
            self::OPTION_ACCESS_GRANTED_WHITELIST => array(
                'type' => self::TYPE_MULTICHECKBOX,
                'category' => '',
                'subcategory' => '',
                'title' => 'Restrict dashboard access',
                'desc' => 'Dashboard access can be restricted to users not from whitelist.',
            ),
            self::OPTION_ACCESS_DENIED_LOGGED_IN_USERS => array(
                'type' => self::TYPE_BOOL,
                'category' => '',
                'subcategory' => '',
                'title' => '',
                'desc' => '',
                'default' => '0',
            )
		));

	}

}