<?php
namespace com\cminds\registration\addon\approvenewusers\controller;

use com\cminds\registration\addon\approvenewusers\App;
use com\cminds\registration\addon\approvenewusers\model\User;
use com\cminds\registration\addon\approvenewusers\model\Labels;

class UserController extends Controller {
	
	const PAGE_TITLE = 'Pending Registrations';
	
	static $actions = array(
		'admin_menu' => array('priority' => 11),
	);
	
	static function admin_menu() {
		add_submenu_page(App::PARENT_MENU, App::getPluginName() . ': ' . static::PAGE_TITLE, static::PAGE_TITLE, 'manage_options', self::getMenuSlug(), array(get_called_class(), 'render'));
	}
	
	static function getMenuSlug() {
		return App::SLUG;
	}
	
	static function render() {
		wp_enqueue_style('cmreganu-backend');
		wp_enqueue_script('cmreganu-backend');
		
		$currentUrl = add_query_arg('page', static::getMenuSlug(), admin_url('admin.php'));
		
		$message = '';
		$action = filter_input(INPUT_GET, RegistrationController::PARAM_ADMIN_APPROVE);
		switch ($action) {
			case RegistrationController::ACTION_ADMIN_APPROVE:
				$message = 'User account has been approved.';
				break;
			case RegistrationController::ACTION_ADMIN_REJECT:
				$message = 'User account has been rejected and deleted.';
				break;
		}
		
		echo self::loadView('backend/template', array(
			'title' => App::getPluginName() . ': ' . static::PAGE_TITLE,
			'nav' => self::getBackendNav(),
			'content' => self::loadBackendView('users-approvals', array(
				'users' => User::selectPendingUsers(),
				'currentUrl' => $currentUrl,
				'message' => $message,
			)),
		));
	}
	
}