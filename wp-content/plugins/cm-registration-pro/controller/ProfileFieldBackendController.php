<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\App;
use com\cminds\registration\model\ProfileField;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\User;
use com\cminds\registration\model\Labels;

class ProfileFieldBackendController extends Controller {
		
	const ACTION_EDIT = 'cmreg_profile_edit';
	
	const PAGE_TITLE = 'Profile Fields';
	const PAGE_SLUG = 'profile-fields';
	
	const PAGE_SLUG_EDIT_PROFILE_FIELDS = 'cmreg-edit-profile-fields';
	
	const NONCE_EXPORT_CSV = 'cmreg_extra_fields_export_csv';
	const ACTION_EXPORT_CSV = 'cmreg-extra-fields-csv';
	
	protected static $actions = array(
		array('name' => 'admin_menu', 'priority' => 11),
		'edit_user_profile' => array('args' => 1, 'method' => 'show_user_profile'),
		'show_user_profile' => array('args' => 1),
	);
	
	static $filters = array(
		'user_row_actions' => array('args' => 2),
	);
	
	static $ajax = array(
		'cmreg_user_profile_edit',
	);
	
	static function admin_menu() {
		if (App::isPro()) {
			add_submenu_page(App::SLUG, App::getPluginName() . ' ' . static::PAGE_TITLE, static::PAGE_TITLE,
					'manage_options', self::getMenuSlug(), array(get_called_class(), 'render'));
			add_submenu_page(App::SLUG . '-fake', App::getPluginName() . ' ' . static::PAGE_TITLE, static::PAGE_TITLE,
					'edit_users', static::PAGE_SLUG_EDIT_PROFILE_FIELDS, array(get_called_class(), 'editProfileFields'));
		}
	}
	
	static function getMenuSlug() {
		return App::SLUG .'-' . static::PAGE_SLUG;
	}
	
	static function render() {
		wp_enqueue_style('cmreg-backend');
		wp_enqueue_style('cmreg-settings');
		wp_enqueue_style('cmreg-form-builder');
		wp_enqueue_script('cmreg-backend');
		wp_enqueue_script('cmreg-backend-profile-fields');
		
		ProfileField::recreateDefaultFields();
		
		$fields = ProfileField::getAll();
		$fieldsData = ProfileField::getJSData();
		
		echo self::loadView('backend/template', array(
			'title' => App::getPluginName() . ' ' . static::PAGE_TITLE,
			'nav' => self::getBackendNav(),
			'content' => self::loadBackendView('index', array(
				'nonce' => wp_create_nonce(self::getMenuSlug()),
				'fields' => $fields,
				'fieldsData' => $fieldsData,
				'roles' => Settings::getRolesOptions(),
			)),
		));
	}
	
	static function processRequest() {
		
		// Edit profile fields
		$fileName = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		if (is_admin() AND $fileName == 'admin.php' AND !empty($_GET['page']) AND $_GET['page'] == self::getMenuSlug() AND !empty($_POST)) {
			
			// CSRF protection
			if ((empty($_POST['nonce']) OR !wp_verify_nonce($_POST['nonce'], self::getMenuSlug()))) {
				// Invalid nonce
				die('cmreg profile fields: nonce error');
			}
			
			$data = json_decode(filter_input(INPUT_POST, 'data'), true);

			$oldIds = ProfileField::getAllIds();
			$newIds = array();
			if (!empty($data) AND is_array($data)) {
				foreach ($data as $i => $item) {
					$field = ProfileField::getInstance((string)$item['name']);
					if (!$field) {
						$field = ProfileField::create($item['name'], $item['label'], $item['type']);
					} else {
						$field->setLabel($item['label'])->setUserMetaKey($item['name'])->setFieldType($item['type']);
					}
					$field->setMenuOrder($i+1)->save();
					$field->setRequired(!empty($item['required']));
					$field->setSubtype(!empty($item['subtype']) ? $item['subtype'] : null);
					$field->setTooltip(!empty($item['description']) ? $item['description'] : null);
					$field->setPlaceholder(!empty($item['placeholder']) ? $item['placeholder'] : null);
					$field->setDefaultValue(!empty($item['value']) ? $item['value'] : null);
					$field->setCSSClass(!empty($item['className']) ? $item['className'] : null);
					$field->setTextareaRows(!empty($item['rows']) ? $item['rows'] : null);
					$field->setMaxLength(!empty($item['maxlength']) ? $item['maxlength'] : null);
					$field->setNumberMin(!empty($item['min']) ? $item['min'] : null);
					$field->setNumberMax(!empty($item['max']) ? $item['max'] : null);
					$field->setNumberStep(!empty($item['step']) ? $item['step'] : null);
					$field->setMultipleSelectionAllowed(!empty($item['multiple']) ? $item['multiple'] : false);
					$field->setOptionsValues(!empty($item['values']) ? $item['values'] : null);
					$field->setRoles((!empty($item['access']) AND !empty($item['roles'])) ? $item['roles'] : null);
					$field->setShowInRegistrationForm(!empty($item['showInRegistration']));
					$field->setShowInUserProfile(!empty($item['showInProfile']));
					$field->setRegistrationFormRole(!empty($item['registrationFormRole']) ? $item['registrationFormRole'] : null);
					$newIds[] = $field->getId();
				}
			}
			
			$toDelete = array_diff($oldIds, $newIds);
			foreach ($toDelete as $id) {
				wp_delete_post($id, true);
			}
			
		}
		
		// Export CSV
		if (is_admin()) {
			if (static::ACTION_EXPORT_CSV == filter_input(INPUT_GET, 'action') AND wp_verify_nonce(filter_input(INPUT_GET, 'nonce'), static::NONCE_EXPORT_CSV)) {
		
				$lines = array(static::getCSVHeader());
		
				$userId = filter_input(INPUT_GET, 'userId');
				if (!empty($userId)) {
					$usersIds = array($userId);
				} else {
					$usersIds = get_users(array('fields' => 'ID', 'orderby' => 'user_registered'));
				}
		
				foreach ($usersIds as $id) {
					$lines = array_merge($lines, array(static::getCSVForUser($id)));
				}
				static::downloadCSV($lines);
		
			}
		}
			
	}

	static function show_user_profile($user) {
		if (!App::isLicenseOk()) return;
	
		//$fields = Settings::getOption(Settings::OPTION_REGISTER_EXTRA_FIELDS);
		$fields = ProfileField::getAll();
		//if (is_array($fields)) foreach ($fields as $i => &$field) {
 		//	if ($i == 0) continue;
 		//	$field['value'] = User::getExtraField($user->ID, $field['meta_name']);
 		//}

		$userId = $user->ID;
	
		if (!empty($fields)) {
			echo static::loadBackendView('user-profile', compact('fields', 'userId'));
		}
	}

	static function getExportCSVUrl($userId = null) {
		return add_query_arg(array(
			'action' => static::ACTION_EXPORT_CSV,
			'userId' => $userId,
			'nonce' => wp_create_nonce(static::NONCE_EXPORT_CSV),
		), admin_url('admin.php'));
	}
	
	static function getEditProfileFieldsUrl($userId = null) {
		return add_query_arg(array(
			'page' => static::PAGE_SLUG_EDIT_PROFILE_FIELDS,
			'userId' => $userId,
		), admin_url('admin.php'));
	}
	
	static function user_row_actions($actions, $user) {
		$url = static::getExportCSVUrl($user->ID);
		$actions['cmreg_extra_fields_csv'] = sprintf('<a href="%s">%s</a>', esc_attr($url), 'Extra fields to CSV');
		
		$url = static::getEditProfileFieldsUrl($user->ID);
		$actions['cmreg_edit_extra_fields'] = sprintf('<a href="%s">%s</a>', esc_attr($url), 'Edit profile fields');
		
		return $actions;
	}

	static function downloadCSV($lines) {
		header('content-type: text/csv');
		header('Content-Disposition: attachment; filename="cm-registration-profile-fields.csv"');
		$out = fopen('php://output', 'w');
		foreach ($lines as $line) {
			fputcsv($out, $line);
		}
		fclose($out);
		exit;
	}
	
	static function getCSVForUser($userId) {
		$user = get_userdata($userId);
		$result = array(
			$userId,
			$user->user_login,
			$user->user_email,
			$user->display_name
		);
		$fields = ProfileField::getAll();
		foreach ($fields as $field) {
			if($field->getRegistrationFormRole() == '' || $field->getRegistrationFormRole() == 'invitation_code') {
				$value = $field->getValueForUser($userId);
				if (is_array($value)) {
					$value = implode(', ', $value);
				}
				else if (!is_scalar($value)) {
					$value = json_encode($value);
				}
				$result[] = $value;
			}
		}
		return apply_filters('cmreg_get_csv_row_for_user', $result, $userId);
	}
	
	static function getCSVHeader() {
		$header = array('user_id', 'user_login', 'user_email', 'display_name');
		$fields = Settings::getOption(Settings::OPTION_REGISTER_EXTRA_FIELDS);
		$fields = ProfileField::getAll();
		foreach ($fields as $field) {
			if($field->getRegistrationFormRole() == '' || $field->getRegistrationFormRole() == 'invitation_code') {
				$header[] = $field->getUserMetaKey();
			}
		}
		return apply_filters('cmreg_get_userdata_csv_header', $header);
	}
	
	static function editProfileFields() {
		wp_enqueue_style('cmreg-backend');
		wp_enqueue_style('cmreg-settings');
		wp_enqueue_script('cmreg-backend');
		wp_enqueue_style('cmreg-frontend');
		wp_enqueue_script('cmreg-profile-edit');
		
		$userId = filter_input(INPUT_GET, 'userId');
		$user = get_userdata($userId);
		$userEditUrl = add_query_arg(array('user_id' => $userId), admin_url('user-edit.php'));
		$nonce = wp_create_nonce(static::ACTION_EDIT);
		
		$atts = array();
		$content = UserController::loadFrontendView('profile-edit-form', compact('atts', 'nonce', 'userId'));
		
		echo self::loadView('backend/template', array(
			'title' => App::getPluginName() . ' - ' . 'Edit profile fields',
			'nav' => self::getBackendNav(),
			'content' => static::loadBackendView('edit-profile-fields', compact('content', 'userEditUrl', 'user')),
		));
	}
	
	static function cmreg_user_profile_edit() {
		global $wpdb;

		$isadmin = filter_input(INPUT_POST, 'isadmin');

		$response = array('success' => 0, 'msg' => 'An error occurred. Please try again.', 'isadmin' => $isadmin);
		
		$nonce = filter_input(INPUT_POST, 'nonce');
		if ($nonce AND wp_verify_nonce($nonce, static::ACTION_EDIT)) {
			
			$userId = filter_input(INPUT_POST, 'userId');
			if (current_user_can('edit_users') AND !empty($userId) AND $userId != get_current_user_id()) {
				
			} else {
				$userId = get_current_user_id();
			}
			
			$userdata = User::getUserData($userId);
			
			$user_email = filter_input(INPUT_POST, 'email');

			$fuser_email_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."users where user_email='".$user_email."' AND ID != '".$userId."'" );
			if($fuser_email_count == '0') {
				if (!is_admin() && Settings::getOption(Settings::OPTION_EMAIL_CONFIRM_ENABLE)) {
					if($userdata->user_email != $user_email) {

						$key = rand(99999,9999999999);

						if ( is_multisite() ) {
							$blogname = $GLOBALS['current_site']->site_name;
						} else {
							$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
						}
						$title = Settings::getOption(Settings::OPTION_EDIT_PROFILE_CONFIRM_EMAIL_SUBJECT);
						$title = str_replace('[blogname]', $blogname, $title);

						$url = home_url('/');
						$msg = Settings::getOption(Settings::OPTION_EDIT_PROFILE_CONFIRM_EMAIL_BODY);
						$msg = str_replace('[userfirstname]', get_user_meta($userId, 'first_name', true), $msg);
						$msg = str_replace('[userlastname]', get_user_meta($userId, 'last_name', true), $msg);
						$msg = str_replace('[userlogin]', $userdata->user_login, $msg);
						$msg = str_replace('[siteurl]', site_url('/'), $msg);
						$msg = str_replace('[confirmemailurl]', $url."?cmreg-confirm-email-key=$key");

						$headers = array('Content-Type: text/html; charset=UTF-8');

						wp_mail($userdata->user_email, wp_specialchars_decode($title), wpautop($msg), $headers);

						update_user_meta($userdata->ID, 'cmreg_email_comfirm', $user_email);
						update_user_meta($userdata->ID, 'cmreg_email_comfirm_key', $key);
					}
				} else {
					$userdata->user_email = $user_email;
				}
			} else {
				$response['success'] = 0;
				$response['msg'] = Labels::getLocalized('user_profile_email_used');
				header('content-type: application/json');
				echo json_encode($response);
				exit;
			}

			$userdata->display_name = filter_input(INPUT_POST, 'display_name');
			$userdata->description = filter_input(INPUT_POST, 'description');
			$userdata->user_url = filter_input(INPUT_POST, 'website');
			
			try {
				
				$invitation_code = filter_input(INPUT_POST, 'invitation_code');
				if(isset($_POST['invitation_code']) && $invitation_code != '') {
					$results = $wpdb->get_row( "select post_id from $wpdb->postmeta where meta_value = '".$invitation_code."'", ARRAY_A );
					if(isset($results['post_id'])) {
						update_user_meta($userdata->ID, 'cmreg_invitation_code', $results['post_id']);
						update_user_meta($userdata->ID, 'cmreg_invitation_code_string', $invitation_code);
					} else {
						throw new \Exception('Invalid invitation code.');
					}
				}

				User::updateUserData($userdata);
				do_action('cmreg_user_profile_edit_save', $userdata->ID);
				//static::processSaveExtraFields($userdata);
				
				$response['success'] = 1;
				$response['msg'] = Labels::getLocalized('user_profile_edit_success');
				
			} catch (\Exception $e) {
				$response['msg'] = $e->getMessage();
			}
		}
		header('content-type: application/json');
		echo json_encode($response);
		exit;
	}
	
	/*
	protected static function processSaveExtraFields($userdata) {
		$extraFieldsValues = (isset($_POST['extra_field']) ? $_POST['extra_field'] : array());
		$extraFields = Settings::getOption(Settings::OPTION_REGISTER_EXTRA_FIELDS);
		if (is_array($extraFields)) {
			array_shift($extraFields);
			foreach ($extraFields as $i => &$field) {
				$name = $field['meta_name'];
				$value = (isset($extraFieldsValues[$name]) ? $extraFieldsValues[$name] : '');
				User::setExtraField($userdata->ID, $name, $value);
			}
		}
	}
	*/

}