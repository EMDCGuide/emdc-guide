<?php
namespace com\cminds\siteaccessrestriction\helper;

abstract class RestrictedResource {
	
	const RESTRICTION_NONE = 'none';
	const RESTRICTION_LOGGED_IN_USERS = 'loggedin';
	const RESTRICTION_GUESTS = 'guests';
	const RESTRICTION_ROLES = 'roles';
	const RESTRICTION_GLOBAL = 'global';
    const RESTRICTION_DAYS = 'days';
    const RESTRICTION_BLACKLIST = 'blacklist';
    const RESTRICTION_WHITELIST = 'whitelist';
    const RESTRICTION_USERFIRSTACCESS = 'userfirstaccess';
	const RESTRICTION_SPECIFICDATE = 'specificdate';
	
	static $restrictionNames = array(
		self::RESTRICTION_NONE => 'Access not restricted',
		self::RESTRICTION_LOGGED_IN_USERS => 'Access restricted to logged-in users',
		self::RESTRICTION_GUESTS => 'Allow only anonymous access',
		self::RESTRICTION_ROLES => 'Access restricted by role',
		self::RESTRICTION_GLOBAL => 'Follow access global options for this post type',
        self::RESTRICTION_DAYS => 'Access denied X days from registration',
        self::RESTRICTION_BLACKLIST => 'Access restricted by blacklist',
        self::RESTRICTION_WHITELIST => 'Access restricted by whitelist',
        self::RESTRICTION_USERFIRSTACCESS => 'Access restricted from the user first access',
		self::RESTRICTION_SPECIFICDATE => 'Access restricted by specific date/time',
	);

	static $restrictionNamesShort = array(
		self::RESTRICTION_NONE => 'not restricted',
		self::RESTRICTION_LOGGED_IN_USERS => 'logged-in users',
		self::RESTRICTION_GUESTS => 'guests',
		self::RESTRICTION_ROLES => 'by role',
		self::RESTRICTION_GLOBAL => 'follow global',
        self::RESTRICTION_DAYS => 'by days',
        self::RESTRICTION_BLACKLIST => 'by blacklist',
        self::RESTRICTION_WHITELIST => 'by whitelist',		
        self::RESTRICTION_USERFIRSTACCESS => 'from the user first access',
		self::RESTRICTION_SPECIFICDATE => 'by specific date/time',
	);
	
	protected $resource;
	
	function __construct($resource) {
		$this->resource = $resource;
	}
	
	function getResource() {
		return $this->resource;
	}
	
	abstract function getRestriction();
	abstract function setRestriction($val);
	abstract function getAllowedRoles();
	abstract function setAllowedRoles($roles);
	abstract function getGuests();
	abstract function setGuests($val);
	abstract function getAllowedUsers();
	abstract function setAllowedUsers($users);
	abstract function getNotAllowedUsers();
	abstract function setNotAllowedUsers($users);
    abstract function getRestrictedDays();
    abstract function getRestrictedDaysFromFirstAccess();
    abstract function getRestrictedFromDate();
    abstract function getRestrictedToDate();
    abstract function setRestrictedDays($roles);
	abstract function getGlobalResource();
	abstract function getRestrictedMode();
	abstract function setRestrictedMode($type);
	
	function canView($userId = null) {
		if (is_null($userId)) {
			$userId = get_current_user_id();
		}
		$restriction = $this->getRestriction();
		switch ($restriction) {
			case static::RESTRICTION_GLOBAL:
				return $this->canViewGlobal($userId);
			case static::RESTRICTION_LOGGED_IN_USERS:
				return !empty($userId);
            case static::RESTRICTION_GUESTS:
                return empty($userId);
			case static::RESTRICTION_ROLES:
				return $this->canViewRole($userId);
            case static::RESTRICTION_BLACKLIST:
                return $this->cantViewUsers($userId);
            case static::RESTRICTION_WHITELIST:
                return $this->canViewUsers($userId);
			case static::RESTRICTION_SPECIFICDATE:
				return $this->canViewDates($userId);
			case static::RESTRICTION_NONE:
			default:
				return true;
		}
	}
	
	function canViewGlobal($userId) {
		if ($resource = $this->getGlobalResource()) {
			return $resource->canView($userId);
		} else {
			return true;
		}
	}
	
	function getRestrictionLabel($short = false) {
		$restriction = $this->getRestriction();
		return ($short ? self::$restrictionNamesShort[$restriction] : self::$restrictionNames[$restriction]);
	}
	
	function canViewDates($userId) {
		$now = date('Y-m-d');
		$from_date = $this->getRestrictedFromDate();
		$to_date = $this->getRestrictedToDate();
		if($now >= $from_date && $now <= $to_date) {
			return true;
		} else {
			return false;
		}
	}

	function canViewRole($userId) {
		$roles = $this->getAllowedRoles();
		if (empty($roles)) return false;
		if ($user = get_userdata($userId)) {
			$inner = array_intersect($user->roles, $roles);
			return !empty($inner);
		}
		return false;
	}

	function canViewUsers($userId) {
	    $users = $this->getAllowedUsers();
	    if (empty($users)) return false;
	    return in_array($userId, $users);
    }

    function cantViewUsers($userId) {
	    if (empty($userId)) return false;
        $users = $this->getNotAllowedUsers();
        if (empty($users)) return true;
        return !in_array($userId, $users);
    }

    function canViewDays($userId = null) {
        if (is_null($userId)) {
            $userId = get_current_user_id();
        }
        if ($user_data = get_userdata($userId)) {
			
			//$this->getRestriction();
			//$this->getRestrictedDaysFromFirstAccess();
			//$this->getResource()->getID();

            $registered = strtotime($user_data->user_registered);
            $restrictedDays = $this->getRestrictedDays() * 24 * 3600;
            if (($registered + $restrictedDays) > time())
                return false;
        }
        return true;
    }

    function canViewCategory($userId = null) {
        if (is_null($userId)) {
            $userId = get_current_user_id();
        }
        if (empty($userId)) {
            $user_roles = array();
        } else {
            $user_data = get_userdata($userId);
            $user_roles = $user_data->roles;
        }

		if(is_category()) {
			$cat = get_category(get_queried_object_id());
			if($cat) {
				$selectedRoles = get_term_meta(get_queried_object_id(), 'cmsar_allowed_roles', TRUE);
				if (!empty($selectedRoles)){
					$inner = array_intersect($user_roles, $selectedRoles);
					if(count($inner) == 0) {
						return false;
					}
				}
			}
		} else if(is_tax()) {
			$selectedRoles = get_term_meta(get_queried_object_id(), 'cmsar_allowed_roles', TRUE);
			if (!empty($selectedRoles)){
				$inner = array_intersect($user_roles, $selectedRoles);
				if(count($inner) == 0) {
					return false;
				}
			}
		}
		
		/*
	    if (is_object($this->getResource())){

            $post_id    = $this->getResource()->getID();
            $categories = get_the_category($post_id);

            if (!empty($categories) && is_array($categories)){
                foreach($categories as $category){
                    $selectedRoles = get_term_meta($category->term_id, 'cmsar_allowed_roles', TRUE);
                    if (!empty($selectedRoles)){
                        $inner = array_intersect($user_roles, $selectedRoles);
                        if(empty($inner)) return false;
                    }
                }
            } else {
                $post = get_post($post_id);
                $taxonomies = get_object_taxonomies($post->post_type, 'objects');
                foreach ($taxonomies as $taxonomy){
                    $terms = get_the_terms($post_id, $taxonomy->name);
                    if (is_array($terms) && !empty($terms)){
                        foreach ($terms as $category){
                            $selectedRoles = get_term_meta($category->term_id, 'cmsar_allowed_roles', TRUE);
                            if (!empty($selectedRoles)){
                                $inner = array_intersect($user_roles, $selectedRoles);
                                if(empty($inner)) return false;
                            }
                        }
                    }
                }
            }
        }
		*/

        return true;
    }

}
?>