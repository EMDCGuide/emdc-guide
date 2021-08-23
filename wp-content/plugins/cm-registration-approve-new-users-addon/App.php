<?php
namespace com\cminds\registration\addon\approvenewusers;

use com\cminds\registration\addon\approvenewusers\core\Core;
use com\cminds\registration\addon\approvenewusers\model\Settings;

require_once dirname(__FILE__) . '/core/Core.php';

class App extends Core {
	
	const VERSION = '1.1.2';
	const PREFIX = 'cmreganu';
	const SLUG = 'cm-registration-approve-new-users';
	const PLUGIN_NAME = 'CM Registration Approve New Users Addon';
	const PLUGIN_WEBSITE = '';
	const PARENT_PLUGIN_SETTINGS_MENU_SLUG = 'cm-registration-settings';
	const PARENT_PREFIX = 'cmreg';
	const PARENT_SLUG = 'cm-registration';
	const PARENT_MENU = 'cm-registration';
	
	
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
		
		wp_register_script('cmreganu-utils', static::url('asset/js/utils.js'), array('jquery'), static::VERSION, true);
		wp_register_script('cmreganu-account-approval', static::url('asset/js/account-approval.js'), array('jquery', 'cmreganu-utils'), static::VERSION, true);
		wp_register_script('cmreganu-backend', static::url('asset/js/backend.js'), array('jquery'), static::VERSION, true);
		
// 		wp_register_style('cmreganu-settings', static::url('asset/css/settings.css'), null, static::VERSION);
		wp_register_style('cmreganu-backend', static::url('asset/css/backend.css'), null, static::VERSION);
		wp_register_style('cmreganu-frontend', static::url('asset/css/frontend.css'), array(), static::VERSION);
		
		wp_register_script('cmreganu-frontend', static::url('asset/js/frontend.js'), array('jquery'), static::VERSION, true);
		
	}
	

	static function admin_menu() {
		parent::admin_menu();
		$name = static::getPluginName(true);
// 		$page = add_menu_page($name, $name, 'manage_options', static::PREFIX,
// 			array(App::namespaced('controller\SettingsController'), 'render'), 'dashicons-admin-users', 5679);
// 		add_menu_page($name, $name, 'manage_options', static::PREFIX); //, array(App::namespaced('controller\SettingsController'), 'render'));
	}
	
	
}
