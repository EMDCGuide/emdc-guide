<?php
namespace com\cminds\siteaccessrestriction\helper;

use com\cminds\siteaccessrestriction\model\Post;

class PostTypeRestriction extends RestrictedResource {
	
	const OPTION_POST_TYPE_RESTRICTION_PREFIX = 'cmacc_post_type_restriction_';
	const OPTION_POST_TYPE_ALLOWED_ROLES_PREFIX = 'cmacc_postt_roles_';
	const OPTION_POST_TYPE_RESTRICTED_DAYS_PREFIX = 'cmacc_restricted_days_';
	const OPTION_POST_TYPE_RESTRICTED_DAYS_FROM_FIRST_ACCESS_PREFIX = 'cmacc_restricted_days_from_first_access_';
	const OPTION_POST_TYPE_RESTRICTED_FROM_DATE_PREFIX = 'cmacc_restricted_from_date_';
	const OPTION_POST_TYPE_RESTRICTED_TO_DATE_PREFIX = 'cmacc_restricted_to_date_';
	const OPTION_POST_TYPE_RESTRICTED_MODE = 'cmacc_restricted_mode_';

	const OPTION_POST_TYPE_RESTRICTED_GUESTS = 'cmacc_post_type_restricted_guests_';

	const OPTION_POST_TYPE_ALLOWED_USERS_PREFIX = 'cmacc_post_type_allowed_users_';
	const OPTION_POST_TYPE_NOT_ALLOWED_USERS_PREFIX = 'cmacc_post_type_not_allowed_users_';

	function getPostTypeName() {
		return $this->getResource();
	}
	
	function getRestriction() {
		return get_option(self::OPTION_POST_TYPE_RESTRICTION_PREFIX . $this->getPostTypeName(), self::RESTRICTION_NONE);
	}
	
	function setRestriction($val) {
		update_option(self::OPTION_POST_TYPE_RESTRICTION_PREFIX . $this->getPostTypeName(), $val, $autoload = true);
		return $this;
	}
	
	function getAllowedRoles() {
		$optionName = self::OPTION_POST_TYPE_ALLOWED_ROLES_PREFIX . $this->getPostTypeName();
		$val = get_option($optionName);
		if (!is_array($val)) $val = array();
		return $val;
	}
	
	function setAllowedRoles($roles) {
		$optionName = self::OPTION_POST_TYPE_ALLOWED_ROLES_PREFIX . $this->getPostTypeName();
		update_option($optionName, $roles, $autoload = true);
	}

	function getGuests() {
        $optionName = self::OPTION_POST_TYPE_RESTRICTED_GUESTS . $this->getPostTypeName();
        return get_option($optionName);
    }

    function setGuests($val) {
        $optionName = self::OPTION_POST_TYPE_RESTRICTED_GUESTS . $this->getPostTypeName();
        update_option($optionName, $val);
    }

    function getAllowedUsers() {
        $optionName = self::OPTION_POST_TYPE_ALLOWED_USERS_PREFIX . $this->getPostTypeName();
        $val = get_option($optionName);
        if (!is_array($val)) $val = array();
        return $val;
    }

    function setAllowedUsers($users) {
        $optionName = self::OPTION_POST_TYPE_ALLOWED_USERS_PREFIX . $this->getPostTypeName();
        update_option($optionName, $users, $autoload = true);
    }

    function getNotAllowedUsers() {
        $optionName = self::OPTION_POST_TYPE_NOT_ALLOWED_USERS_PREFIX . $this->getPostTypeName();
        $val = get_option($optionName);
        if (!is_array($val)) $val = array();
        return $val;
    }

    function setNotAllowedUsers($users) {
        $optionName = self::OPTION_POST_TYPE_NOT_ALLOWED_USERS_PREFIX . $this->getPostTypeName();
        update_option($optionName, $users, $autoload = true);
    }

    function getRestrictedDays() {
        $optionName = self::OPTION_POST_TYPE_RESTRICTED_DAYS_PREFIX . $this->getPostTypeName();
        $val = get_option($optionName);
        if (empty($val)) $val = 0;
        return $val;
    }

    function setRestrictedDays($days) {
        $optionName = self::OPTION_POST_TYPE_RESTRICTED_DAYS_PREFIX . $this->getPostTypeName();
        update_option($optionName, $days, $autoload = true);
    }
	
	function getRestrictedDaysFromFirstAccess() {
        $optionName = self::OPTION_POST_TYPE_RESTRICTED_DAYS_FROM_FIRST_ACCESS_PREFIX . $this->getPostTypeName();
        $val = get_option($optionName);
        if (empty($val)) $val = 0;
        return $val;
    }

	function setRestrictedDaysFromFirstAccess($days) {
        $optionName = self::OPTION_POST_TYPE_RESTRICTED_DAYS_FROM_FIRST_ACCESS_PREFIX . $this->getPostTypeName();
        update_option($optionName, $days, $autoload = true);
    }

	function getRestrictedFromDate() {
        $optionName = self::OPTION_POST_TYPE_RESTRICTED_FROM_DATE_PREFIX . $this->getPostTypeName();
        $val = get_option($optionName);
        if (empty($val)) $val = date('Y-m-d');
        return $val;
    }

	function setRestrictedFromDate($date) {
        $optionName = self::OPTION_POST_TYPE_RESTRICTED_FROM_DATE_PREFIX . $this->getPostTypeName();
        update_option($optionName, $date, $autoload = true);
    }

	function getRestrictedToDate() {
        $optionName = self::OPTION_POST_TYPE_RESTRICTED_TO_DATE_PREFIX . $this->getPostTypeName();
        $val = get_option($optionName);
        if (empty($val)) $val = date('Y-m-d');
        return $val;
    }

	function setRestrictedToDate($date) {
        $optionName = self::OPTION_POST_TYPE_RESTRICTED_TO_DATE_PREFIX . $this->getPostTypeName();
        update_option($optionName, $date, $autoload = true);
    }
	
	function getGlobalResource() {
		return null;
	}

	function setRestrictedMode($mode) {
		$optionName = self::OPTION_POST_TYPE_RESTRICTED_MODE . $this->getPostTypeName();
		update_option($optionName, $mode, $autoload = true);
	}
	
	function getRestrictedMode() {
		$optionName = self::OPTION_POST_TYPE_RESTRICTED_MODE . $this->getPostTypeName();
		$val = get_option($optionName);
		if (empty($val)) $val = 'global';
		return $val;
	}

}