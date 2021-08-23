<?php
namespace com\cminds\siteaccessrestriction\metabox;

use com\cminds\siteaccessrestriction\helper\RestrictionSettings;
use com\cminds\siteaccessrestriction\helper\PostRestriction;
use com\cminds\siteaccessrestriction\model\Settings;
use com\cminds\siteaccessrestriction\model\Post;
use com\cminds\siteaccessrestriction\controller\PostController;
use com\cminds\siteaccessrestriction\helper\FormHtml;

class PostAccessBox extends MetaBox {

	const SLUG = 'cmacc-post-access';
	const NAME = 'Site Access Restriction';
	const CONTEXT = 'side';
	
	const FIELD_RESTRICTION = 'cmacc-restriction';
	const FIELD_ROLE = 'cmacc-roles';

	const FIELD_ALLOWED_USERS = 'cmacc-allowed-users';
	const FIELD_NOT_ALLOWED_USERS = 'cmacc-not-allowed-users';

	const FIELD_DAYS = 'cmacc-days';
	const FIELD_DAYS_FROM_FIRST_ACCESS = 'cmacc-days-from-first-access';
	const FIELD_FROM_DATE = 'cmacc-from-date';
	const FIELD_TO_DATE = 'cmacc-to-date';
	const FIELD_MODE = 'cmacc-mode';
	
	static function render($post) {
		wp_enqueue_style('cmacc-backend');
		wp_enqueue_script('cmacc-backend');
		
		static::renderNonceField($post);
		
		$post = Post::getInstance($post);
		$postRestriction = new PostRestriction($post);
		echo RestrictionSettings::displaySettings(
			$restrictedResource = $postRestriction,
			$restrictionFieldName = self::FIELD_RESTRICTION,
			$roleFieldName = self::FIELD_ROLE,

            $allowedUsersFieldName = self::FIELD_ALLOWED_USERS,
            $notAllowedUsersFieldName = self::FIELD_NOT_ALLOWED_USERS,

            $daysFieldName = self::FIELD_DAYS,
            $daysFromFirstAccessFieldName = self::FIELD_DAYS_FROM_FIRST_ACCESS,
            $fromDateFieldName = self::FIELD_FROM_DATE,
            $toDateFieldName = self::FIELD_TO_DATE
		);
	}
	
	static function savePost($post_id) {
		if ($post = Post::getInstance($post_id)) {
			$postRestriction = new PostRestriction($post);
			
			if ($restriction = filter_input(INPUT_POST, self::FIELD_RESTRICTION)) {
				$postRestriction->setRestriction($restriction);
			}
			
			if (empty($_POST[self::FIELD_ROLE])) {
				$roles = array();
			} else {
				$roles = $_POST[self::FIELD_ROLE];
			}
			$postRestriction->setAllowedRoles($roles);

            if (empty($_POST[self::FIELD_ALLOWED_USERS])) {
                $allowedUsers = array();
            } else {
                $allowedUsers = $_POST[self::FIELD_ALLOWED_USERS];
            }
            $postRestriction->setAllowedUsers($allowedUsers);

            if (empty($_POST[self::FIELD_NOT_ALLOWED_USERS])) {
                $notAllowedUsers = array();
            } else {
                $notAllowedUsers = $_POST[self::FIELD_NOT_ALLOWED_USERS];
            }
            $postRestriction->setNotAllowedUsers($notAllowedUsers);

            $days = filter_input(INPUT_POST, self::FIELD_DAYS);
            $postRestriction->setRestrictedDays($days);

			$daysFromFirstAccess = filter_input(INPUT_POST, self::FIELD_DAYS_FROM_FIRST_ACCESS);
            $postRestriction->setRestrictedDaysFromFirstAccess($daysFromFirstAccess);

			$fromDate = filter_input(INPUT_POST, self::FIELD_FROM_DATE);
            $postRestriction->setRestrictedFromDate($fromDate);

			$toDate = filter_input(INPUT_POST, self::FIELD_TO_DATE);
            $postRestriction->setRestrictedToDate($toDate);

			$mode = $_POST[self::FIELD_MODE];				
			$postRestriction->setRestrictedMode($mode);
		}
	}
	
	static function getSupportedPostTypes() {
		return array_keys(Settings::getPostTypesOptions());
	}
	
}