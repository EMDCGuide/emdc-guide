<?php
namespace com\cminds\siteaccessrestriction\helper;

use com\cminds\siteaccessrestriction\model\Post;

class UrlRestriction extends RestrictedResource {
	
	const OPTION_URL_RESTRICTION = 'cmacc_url_restriction';
	const OPTION_URL_ALLOWED_ROLES = 'cmacc_url_roles';
    const OPTION_URL_RESTRICTED_DAYS = 'cmacc_url_days';
    const OPTION_URL_RESTRICTED_DAYS_FROM_FIRST_ACCESS = 'cmacc_url_days_from_first_access';
    const OPTION_URL_RESTRICTED_FROM_DATE = 'cmacc_url_from_date';
    const OPTION_URL_RESTRICTED_TO_DATE = 'cmacc_url_to_date';

    const OPTION_URL_GUESTS = 'cmacc_url_guests';

    const OPTION_URL_ALLOWED_USERS = 'cmacc_url_allowed_users';
    const OPTION_URL_NOT_ALLOWED_USERS = 'cmacc_url_not_allowed_users';

	function getRestriction() {
		$resource = $this->getResource();
		$option = get_option(self::OPTION_URL_RESTRICTION, array());
		if ($resource AND $option AND is_array($option) AND isset($option[$resource->getId()])) {
			return $option[$resource->getId()];
		} else {
			return static::RESTRICTION_NONE;
		}
	}
	
	function setRestriction($val) {
		$resource = $this->getResource();
		$option = get_option(self::OPTION_URL_RESTRICTION, array());
		if (!is_array($option)) $option = array();
		$option[$resource->getId()] = $val;
		update_option(self::OPTION_URL_RESTRICTION, $option, $autoload = true);
		return $this;
	}
	
	function getAllowedRoles() {
		$resource = $this->getResource();
		$option = get_option(self::OPTION_URL_ALLOWED_ROLES, array());
		if (!is_array($option)) $option = array();
		if ($resource AND $option AND is_array($option) AND isset($option[$resource->getId()])) {
			return $option[$resource->getId()];
		} else {
			return array();
		}
	}
	
	function setAllowedRoles($roles) {
		$resource = $this->getResource();
		$option = get_option(self::OPTION_URL_ALLOWED_ROLES, array());
		if (!is_array($option)) $option = array();
		$option[$resource->getId()] = $roles;
		update_option(self::OPTION_URL_ALLOWED_ROLES, $option, $autoload = true);
		return $this;
	}

	function getGuests(){
        $resource = $this->getResource();
        $option = get_option(self::OPTION_URL_GUESTS, array());
        if ($resource AND $option AND is_array($option) AND isset($option[$resource->getId()])) {
            return $option[$resource->getId()];
        } else {
			//return static::RESTRICTION_NONE;
            return array();
        }
    }

    function setGuests($val){
        $resource = $this->getResource();
        $option = get_option(self::OPTION_URL_GUESTS, array());
        if (!is_array($option)) $option = array();
        $option[$resource->getId()] = $val;
        update_option(self::OPTION_URL_GUESTS, $option, $autoload = true);
        return $this;
    }

	function getAllowedUsers(){
        $resource = $this->getResource();
        $option = get_option(self::OPTION_URL_ALLOWED_USERS, array());
        if (!is_array($option)) $option = array();
        if ($resource AND $option AND is_array($option) AND isset($option[$resource->getId()])) {
            return $option[$resource->getId()];
        } else {
            return array();
        }
    }

    function setAllowedUsers($users){
        $resource = $this->getResource();
        $option = get_option(self::OPTION_URL_ALLOWED_USERS, array());
        if (!is_array($option)) $option = array();
        $option[$resource->getId()] = $users;
        update_option(self::OPTION_URL_ALLOWED_USERS, $option, $autoload = true);
        return $this;
    }

    function getNotAllowedUsers(){
        $resource = $this->getResource();
        $option = get_option(self::OPTION_URL_NOT_ALLOWED_USERS, array());
        if (!is_array($option)) $option = array();
        if ($resource AND $option AND is_array($option) AND isset($option[$resource->getId()])) {
            return $option[$resource->getId()];
        } else {
            return array();
        }
    }

    function setNotAllowedUsers($users){
        $resource = $this->getResource();
        $option = get_option(self::OPTION_URL_NOT_ALLOWED_USERS, array());
        if (!is_array($option)) $option = array();
        $option[$resource->getId()] = $users;
        update_option(self::OPTION_URL_NOT_ALLOWED_USERS, $option, $autoload = true);
        return $this;
    }

    function getRestrictedDays() {
        $val = get_option(self::OPTION_URL_RESTRICTED_DAYS, 0);
        return $val;
    }

    function setRestrictedDays($days) {
        update_option(self::OPTION_URL_RESTRICTED_DAYS, $days, $autoload = true);
    }
	
	function getRestrictedDaysFromFirstAccess() {
        $val = get_option(self::OPTION_URL_RESTRICTED_DAYS_FROM_FIRST_ACCESS, 0);
        return $val;
    }

    function setRestrictedDaysFromFirstAccess($days) {
        update_option(self::OPTION_URL_RESTRICTED_DAYS_FROM_FIRST_ACCESS, $days, $autoload = true);
    }

	function getRestrictedFromDate() {
        $val = get_option(self::OPTION_URL_RESTRICTED_FROM_DATE, date('Y-m-d'));
        return $val;
    }

    function setRestrictedFromDate($date) {
        update_option(self::OPTION_URL_RESTRICTED_FROM_DATE, $date, $autoload = true);
    }

	function getRestrictedToDate() {
        $val = get_option(self::OPTION_URL_RESTRICTED_TO_DATE, date('Y-m-d'));
        return $val;
    }

    function setRestrictedToDate($date) {
        update_option(self::OPTION_URL_RESTRICTED_TO_DATE, $date, $autoload = true);
    }
	
	function getGlobalResource() {
		return null;
	}

	function getRestrictedMode() {
		return null;
	}
	
	function setRestrictedMode($type) {
		return null;
	}

}
?>