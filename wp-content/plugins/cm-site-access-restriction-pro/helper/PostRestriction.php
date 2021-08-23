<?php
namespace com\cminds\siteaccessrestriction\helper;

use com\cminds\siteaccessrestriction\model\Post;

class PostRestriction extends RestrictedResource {
	
	const META_RESTRICTION = 'cmacc_restriction';
	const META_ALLOWED_ROLES = 'cmacc_allowed_roles';
	const META_RESTRICTED_DAYS = 'cmacc_restricted_days';
	const META_RESTRICTED_DAYS_FROM_FIRST_ACCESS = 'cmacc_restricted_days_from_first_access';
	const META_RESTRICTED_FROM_DATE = 'cmacc_restricted_from_date';
	const META_RESTRICTED_TO_DATE = 'cmacc_restricted_to_date';
	const META_RESTRICTION_VIEW_MODE = 'cmacc_restriction_view_mode';

	const META_ALLOWED_USERS = 'cmacc_allowed_users';
	const META_NOT_ALLOWED_USERS = 'cmacc_not_allowed_users';

	//const META_ACCESS_DENIED_LOGGED_IN_USERS = 'cmacc_access_denied_logged_in';
	const META_GUESTS = 'cmacc_guests';

	function getPost() {
		return $this->getResource();
	}

	function getRestriction() {
		$val = $this->getPost()->getPostMeta(static::META_RESTRICTION);
		if (empty($val)) {
			$val = self::RESTRICTION_GLOBAL;
		}
		return $val;
	}
	
	function setRestriction($val) {
		return $this->getPost()->setPostMeta(static::META_RESTRICTION, $val);
	}
	
	function getAllowedRoles() {
		$val = $this->getPost()->getPostMeta(static::META_ALLOWED_ROLES, false);
		if (!is_array($val)) $val = array();
		return $val;
	}
	
	function setAllowedRoles($roles) {
		return $this->getPost()->updateMultiplePostMeta(
			static::META_ALLOWED_ROLES,
			$roles,
			$current = $this->getAllowedRoles()
		);
	}

	function getAllowedUsers() {
        $val = $this->getPost()->getPostMeta(static::META_ALLOWED_USERS, false);
        if (!is_array($val)) $val = array();
        return $val;
    }

    function setAllowedUsers($users) {
        return $this->getPost()->updateMultiplePostMeta(
            static::META_ALLOWED_USERS,
            $users,
            $current = $this->getAllowedUsers()
        );
    }

    function getNotAllowedUsers() {
        $val = $this->getPost()->getPostMeta(static::META_NOT_ALLOWED_USERS, false);
        if (!is_array($val)) $val = array();
        return $val;
    }

    function setNotAllowedUsers($users) {
        return $this->getPost()->updateMultiplePostMeta(
            static::META_NOT_ALLOWED_USERS,
            $users,
            $current = $this->getNotAllowedUsers()
        );
    }

    function getGuests(){
	    return $this->getPost()->getPostMeta(static::META_GUESTS, true);
	}

    function setGuests($val){
	    return $this->getPost()->setPostMeta(static::META_GUESTS, $val);
    }

    function getRestrictedDays() {
        $val = $this->getPost()->getPostMeta(static::META_RESTRICTED_DAYS);
        if (empty($val)) {
            $post_type = $this->getPost()->getType();
            $val = get_option(PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_DAYS_PREFIX . $post_type);
        }
        return $val;
    }

    function setRestrictedDays($days) {
        return $this->getPost()->setPostMeta(static::META_RESTRICTED_DAYS, $days);
    }
	
	function getRestrictedDaysFromFirstAccess() {
        $val = $this->getPost()->getPostMeta(static::META_RESTRICTED_DAYS_FROM_FIRST_ACCESS);
        if (empty($val)) {
            $post_type = $this->getPost()->getType();
            $val = get_option(PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_DAYS_FROM_FIRST_ACCESS_PREFIX . $post_type);
        }
        return $val;
    }

    function setRestrictedDaysFromFirstAccess($days) {
        return $this->getPost()->setPostMeta(static::META_RESTRICTED_DAYS_FROM_FIRST_ACCESS, $days);
    }

	function getRestrictedFromDate() {
        $val = $this->getPost()->getPostMeta(static::META_RESTRICTED_FROM_DATE);
        if (empty($val)) {
            $post_type = $this->getPost()->getType();
            $val = get_option(PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_FROM_DATE_PREFIX . $post_type);
        }
        return $val;
    }

    function setRestrictedFromDate($date) {
        return $this->getPost()->setPostMeta(static::META_RESTRICTED_FROM_DATE, $date);
    }

	function getRestrictedToDate() {
        $val = $this->getPost()->getPostMeta(static::META_RESTRICTED_TO_DATE);
        if (empty($val)) {
            $post_type = $this->getPost()->getType();
            $val = get_option(PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_TO_DATE_PREFIX . $post_type);
        }
        return $val;
    }

    function setRestrictedToDate($date) {
        return $this->getPost()->setPostMeta(static::META_RESTRICTED_TO_DATE, $date);
    }
	
	function getGlobalResource() {
		$postType = $this->getPost()->getPost()->post_type;
		return new PostTypeRestriction($postType);
	}

	function setRestrictedMode($mode) {
		return $this->getPost()->setPostMeta(static::META_RESTRICTION_VIEW_MODE, $mode);
	}
	
	function getRestrictedMode() {
		$val = $this->getPost()->getPostMeta(static::META_RESTRICTION_VIEW_MODE);
		return $val;		
	}

}