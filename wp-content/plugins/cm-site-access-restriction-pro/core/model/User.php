<?php

namespace com\cminds\siteaccessrestriction\model;

class User extends Model {
	

	static function getSomeAdminUserId() {
		$admins = get_users(array('role' => 'administrator'));
		return ($admins[0]->ID);
	}
	
	
	static function hasRole($role, $userId = null) {
		if (empty($userId)) $userId = get_current_user_id();
		if ($userId AND $user = get_userdata($userId)) {
			$inner = array_intersect(array($role), $user->roles);
			return !empty($inner);
		}
		return false;
	}
	
	
	static function hasCapability($cap, $userId = null) {
		if (empty($userId)) $userId = get_current_user_id();
		return user_can($userId, $cap);
	}
	
}