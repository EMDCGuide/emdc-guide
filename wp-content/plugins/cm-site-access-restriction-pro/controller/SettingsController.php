<?php
namespace com\cminds\siteaccessrestriction\controller;

use com\cminds\siteaccessrestriction\helper\AdminNotice;
use com\cminds\siteaccessrestriction\helper\PostRestriction;
use com\cminds\siteaccessrestriction\model\Labels;
use com\cminds\siteaccessrestriction\App;
use com\cminds\siteaccessrestriction\model\Post;
use com\cminds\siteaccessrestriction\model\Settings;

class SettingsController extends Controller {
	
	const ACTION_CLEAR_CACHE = 'clear-cache';
	
	protected static $actions = array(
		'admin_menu',
		'cmacc_display_available_shortcodes',
	);

	protected static $filters = array(
		array('name' => 'cmacc-settings-category', 'args' => 2, 'method' => 'settingsLabels'),
		array('name' => 'cmacc-settings-category', 'args' => 2, 'method' => 'settingsShortcodes'),
	);

	protected static $ajax = array(
		'cmacc_admin_notice_dismiss',
        'cmacc_search_users',
	);
	
	static function admin_menu() {
		add_submenu_page(App::SLUG, App::getPluginName() . ' Settings', 'Settings', 'manage_options', self::getMenuSlug(), array(get_called_class(), 'render'));
	}
	
	static function getMenuSlug() {
		return App::SLUG; // . '-settings';
	}
	
	static function render() {
		wp_enqueue_style('cmacc-backend');
		wp_enqueue_style('cmacc-settings');
		wp_enqueue_script('cmacc-backend');
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

	static function settingsShortcodes($result, $category) {
		if ($category == 'shortcodes') {
			$result = self::loadBackendView('shortcodes_settings');
		}
		return $result;
	}

	// Save Settings
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
			        self::saveShortcodeSettings($_POST);
			        $response = array('status' => 'ok', 'msg' => 'Settings have been updated.');
			        wp_safe_redirect(self::createBackendUrl(self::getMenuSlug(), $response));
			        exit;
		        } 
			}
			else if (!empty($_GET['action']) AND !empty($_GET['nonce']) AND wp_verify_nonce($_GET['nonce'], $_GET['action'])) switch ($_GET['action']) {
				case self::ACTION_CLEAR_CACHE:
					wp_safe_redirect(self::createBackendUrl(self::getMenuSlug(), array('status' => 'ok', 'msg' => 'Cache has been removed.')));
					exit;
					break;
			}
		}
	}

	public static function saveShortcodeSettings($post_data) {
		$option_name = Settings::OPTION_SHORTCODE_ACCESS_DENIED_TEXT;
		if (!empty($post_data) && isset($post_data[$option_name])) {
			$option_value = stripslashes( $post_data[$option_name] );
			update_option($option_name, $option_value);
		}
	}
	
	static function cmacc_display_available_shortcodes() {
		echo self::loadBackendView('shortcodes');
	}

	static function cmacc_search_users(){

	    $user_nick = sanitize_text_field(filter_input(INPUT_POST, 'user_nick'));

        $response = array();

	    if (strlen($user_nick) > 0){
            $users = get_users(
                array(
                    'search' => $user_nick,
                    'search_columns' => array(
                        'user_nicename'
                    )
                )
            );

            if (count($users) > 0){
                $result = array();

                foreach ($users as $user){
                    $result[$user->ID] = $user->nickname;
                }

                $response['success'] = true;
                $response['users'] = $result;

                header('content-type: application/json');
                echo json_encode($response);
                exit;
            } else {
                $response['success'] = false;
                echo json_encode($response);
                exit;
            }

        } else {
            $response['success'] = false;
            echo json_encode($response);
            exit;
        }
    }
		
}