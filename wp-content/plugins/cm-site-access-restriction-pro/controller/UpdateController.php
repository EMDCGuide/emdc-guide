<?php
namespace com\cminds\siteaccessrestriction\controller;

use com\cminds\siteaccessrestriction\App;

class UpdateController extends Controller {
	
	const OPTION_NAME = 'cmacc_update_methods';

	static function bootstrap() {
		global $wpdb;
		
		if (defined('DOING_AJAX') && DOING_AJAX) return;
		
		$updates = get_option(self::OPTION_NAME);
		if (empty($updates)) $updates = array();
		$count = count($updates);
		
		$methods = get_class_methods(__CLASS__);
		foreach ($methods as $method) {
			if (preg_match('/^update((_[0-9]+)+)$/', $method, $match)) {
				if (!in_array($method, $updates)) {
					call_user_func(array(__CLASS__, $method));
					$updates[] = $method;
				}
			}
		}
		
		if ($count != count($updates)) {
			update_option(self::OPTION_NAME, $updates);
		}
		
	}

}