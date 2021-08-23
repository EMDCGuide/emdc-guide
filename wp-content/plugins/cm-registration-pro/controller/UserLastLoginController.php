<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\model\User;
use com\cminds\registration\model\Settings;

class UserLastLoginController extends Controller {
	
	const COLUMN_LAST_LOGIN_DATE = 'cmreg_login_date';
	
	static $filters = array(
		'manage_users_columns',
		'manage_users_custom_column' => array('args' => 3),
		// 'posts_search' => array('args' => 2),
		// 'post_row_actions' => array('args' => 2),
	);
   
   
	static function manage_users_columns($columns) {
		if (Settings::getOption(Settings::OPTION_LOGIN_LOG_LAST_LOGIN_DATE)) {
			$columns[static::COLUMN_LAST_LOGIN_DATE] = 'Last Login Date';
		}
		return $columns;
	}
	
	
	static function manage_users_custom_column($val, $columnName, $userId) {
		if (static::COLUMN_LAST_LOGIN_DATE == $columnName AND Settings::getOption(Settings::OPTION_LOGIN_LOG_LAST_LOGIN_DATE)) {
			if ($date = get_user_meta( $userId, 'last_login', true )) {
				$val = date_i18n( get_option('date_format'), $date) . ' ' . date_i18n( get_option('time_format'), $date);
			} else {
				$val = '--';
			}
		}
		return $val;
	}
	
	
}

