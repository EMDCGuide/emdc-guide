<?php
namespace com\cminds\registration\addon\approvenewusers\controller;

use com\cminds\registration\addon\approvenewusers\model\SettingsAbstract;
use com\cminds\registration\addon\approvenewusers\helper\AdminNotice;
use com\cminds\registration\addon\approvenewusers\model\Labels;
use com\cminds\registration\addon\approvenewusers\App;
use com\cminds\registration\addon\approvenewusers\model\Settings;

class SettingsController extends Controller {
	
	const ACTION_CLEAR_CACHE = 'clear-cache';
	
	protected static $actions = array(
		//array('name' => 'admin_menu', 'priority' => 15),
		'cmreg_labels_init',
	);

	protected static $filters = array(
		//array('name' => 'cmreganu-settings-category', 'args' => 2, 'method' => 'settingsLabels'),
		'cmreg_options_config' => array('priority' => 20),
		'cmreg_settings_pages',
		'cmreg_settings_pages_groups',
	);

	protected static $ajax = array(
		//'cmreganu_admin_notice_dismiss',
	);
	
	static function admin_menu() {
		//add_submenu_page(App::PREFIX, App::getPluginName() . ' Settings', 'Settings', 'manage_options', self::getMenuSlug(), array(get_called_class(), 'render'));
	}
	
	static function getMenuSlug() {
		return App::PREFIX; // . '-settings';
	}
	
	static function render() {
		wp_enqueue_style('cmreganu-backend');
		wp_enqueue_style('cmreganu-settings');
		wp_enqueue_script('cmreganu-backend');
		echo self::loadView('backend/template', array(
			'title' => App::getPluginName() . ' Settings',
			'nav' => self::getBackendNav(),
			'content' => self::loadBackendView('licensing-box') . self::loadBackendView('settings', array(
				'clearCacheUrl' => self::createBackendUrl(self::getMenuSlug(), array('action' => self::ACTION_CLEAR_CACHE), self::ACTION_CLEAR_CACHE),
			)),
		));
	}
	
	static function settingsLabels($result, $category) {
		if ($category == 'labels') {
			$result = self::loadBackendView('labels');
		}
		return $result;
	}
	
	static function processRequest() {
		$fileName = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		if (is_admin() AND $fileName == 'admin.php' AND !empty($_GET['page']) AND $_GET['page'] == self::getMenuSlug()) {
			
			if (!empty($_POST)) {
				
				// CSRF protection
		        if ((empty($_POST['nonce']) OR !wp_verify_nonce($_POST['nonce'], self::getMenuSlug()))) {
		        	// Invalid nonce
		        } else {
			        Settings::processPostRequest($_POST);
			        Labels::processPostRequest();
			        $response = array('status' => 'ok', 'msg' => 'Settings have been updated.');
			        wp_redirect(self::createBackendUrl(self::getMenuSlug(), $response));
			        exit;
		        }
	            
			}
			else if (!empty($_GET['action']) AND !empty($_GET['nonce']) AND wp_verify_nonce($_GET['nonce'], $_GET['action'])) switch ($_GET['action']) {
				case self::ACTION_CLEAR_CACHE:
					wp_redirect(self::createBackendUrl(self::getMenuSlug(), array('status' => 'ok', 'msg' => 'Cache has been removed.')));
					exit;
					break;
			}
	        
		}
	}
	
	static function cmreg_labels_init() {
		do_action('cmreg_load_label_file', App::path('asset/labels/labels.tsv'));
	}
	
	static function cmreg_options_config($config) {
		return array_merge($config, Settings::getAddonOptionsConfig());
	}
	
	public static function cmreg_settings_pages($pages) {
		$pages[Settings::SETTINGS_CATEGORY_KEY] = 'Approve New Users';
		// 		$end = array_splice($pages, -1, 1);
		// 		$pages = array_merge($pages, array('anonymous' => 'Anonymous Upload'), $end);
		return $pages;
	}
	
	public static function cmreg_settings_pages_groups($subcategories) {
		$subcategories[Settings::SETTINGS_CATEGORY_KEY][Settings::SETTINGS_SUBCATEGORY_GENERAL] = 'General';
		$subcategories[Settings::SETTINGS_CATEGORY_KEY][Settings::SETTINGS_SUBCATEGORY_ADMIN_NOTIFICATIONS] = 'Admin Notifications';
		$subcategories[Settings::SETTINGS_CATEGORY_KEY][Settings::SETTINGS_SUBCATEGORY_OWNER_ADMIN_NOTIFICATIONS] = 'Owner Notifications';
		$subcategories[Settings::SETTINGS_CATEGORY_KEY][Settings::SETTINGS_SUBCATEGORY_USER_NOTIFICATIONS] = 'User Notifications';
		$subcategories[Settings::SETTINGS_CATEGORY_KEY][Settings::SETTINGS_SUBCATEGORY_AUTO_APPROVE_DOMAINS] = 'Automatic Approval';
		return $subcategories;
	}

}