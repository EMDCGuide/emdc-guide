<?php
namespace com\cminds\siteaccessrestriction;

use com\cminds\siteaccessrestriction\model\Labels;
use com\cminds\siteaccessrestriction\core\Core;
use com\cminds\siteaccessrestriction\controller\SettingsController;
use com\cminds\siteaccessrestriction\model\Settings;

require_once dirname(__FILE__) . '/core/Core.php';

class App extends Core {
	
	const VERSION = '1.5.9';
	const PREFIX = 'cmacc';
	const SLUG = 'cm-site-access-restriction';
	const PLUGIN_NAME = 'CM Site Access Restriction';
	const PLUGIN_WEBSITE = 'https://www.cminds.com/store/cm-site-access-restriction-and-invitation-codes-plugin-for-wordpress/';
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
		
		wp_register_script('cmacc-utils', static::url('asset/js/utils.js'), array('jquery'), static::VERSION, true);
		wp_register_script('cmacc-backend', static::url('asset/js/backend.js'), array('jquery'), static::VERSION, true);
		wp_register_script('cmacc-recaptcha', 'https://www.google.com/recaptcha/api.js');
		wp_register_script('cmacc_account_verification', static::url('asset/js/account-verification.js'), array('jquery', 'cmacc-utils'), static::VERSION, true);
		wp_register_script('cmacc-logout', static::url('asset/js/logout.js'), array('jquery', 'heartbeat'), static::VERSION, true);
		
		wp_register_style('cmacc-settings', static::url('asset/css/settings.css'), null, static::VERSION);
		wp_register_style('cmacc-backend', static::url('asset/css/backend.css'), null, static::VERSION);
		wp_register_style('cmacc-frontend', static::url('asset/css/frontend.css'), array('dashicons'), static::VERSION);
		
		wp_register_script('cmacc-frontend', static::url('asset/js/frontend.js'), array('jquery', 'cmacc-utils', 'cmacc-recaptcha'), static::VERSION, true);

		if (is_admin() && !(defined('DOING_AJAX') && DOING_AJAX)) {
			$user = get_userdata(get_current_user_id());
			if($user) {
				$role = array_values($user->roles);
				$block_access_to = Settings::getOption(Settings::OPTION_ACCESS_DENIED_USER_ROLES);
				if(isset($role[0]) && in_array($role[0], $block_access_to)) {
					wp_redirect(Settings::getOption(Settings::OPTION_ACCESS_DENIED_REDIRECT_URL));
					exit;
				}
			}
		}
	}
	
	static function admin_menu() {
		parent::admin_menu();
		$name = static::getPluginName(true);
		//$page = add_menu_page($name, $name, 'manage_options', static::PREFIX,
		//array(App::namespaced('controller\UrlController'), 'render'), 'dashicons-admin-users', 5679);
		add_menu_page($name, $name, 'manage_options', static::SLUG, array(App::namespaced('controller\SettingsController'), 'render'));
	}

}