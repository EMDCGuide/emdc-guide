<?php
namespace com\cminds\siteaccessrestriction\controller;

use com\cminds\siteaccessrestriction\helper\RestrictionSettings;
use com\cminds\siteaccessrestriction\helper\PostTypeRestriction;
use com\cminds\siteaccessrestriction\helper\PostRestriction;
use com\cminds\siteaccessrestriction\model\Settings;
use com\cminds\siteaccessrestriction\model\Post;
use com\cminds\siteaccessrestriction\App;

class PostTypesController extends Controller {
	
	static $actions = array(
		'cmacc_settings_after_save',
	);

	static $filters = array(
		'cmacc_options_config',
	);
	
	static function cmacc_options_config($config) {
		$postTypes = Settings::getPostTypesOptions();
		foreach ($postTypes as $postType => $label) {
			$optionName = PostTypeRestriction::OPTION_POST_TYPE_RESTRICTION_PREFIX . $postType;
			$config[$optionName] = array(
				'category' => 'post_types',
				'subcategory' => 'post_types',
				'title' => $label,
				'type' => Settings::TYPE_CUSTOM,
				'content' => self::getSettingsField($optionName, $postType),
			);
		}
		return $config;
	}
	
	static function getSettingsField($optionName, $postType) {
		return RestrictionSettings::displaySettings(
			$restrictedResource = new PostTypeRestriction($postType),
			$restrictionFieldName = PostTypeRestriction::OPTION_POST_TYPE_RESTRICTION_PREFIX . $postType,
			$roleFieldName = PostTypeRestriction::OPTION_POST_TYPE_ALLOWED_ROLES_PREFIX . $postType,

            $allowedUsersFieldName = PostTypeRestriction::OPTION_POST_TYPE_ALLOWED_USERS_PREFIX . $postType,
            $notAllowedUsersFieldName = PostTypeRestriction::OPTION_POST_TYPE_NOT_ALLOWED_USERS_PREFIX . $postType,

            $daysFieldName = PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_DAYS_PREFIX . $postType,
            $daysFromFirstAccessFieldName = PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_DAYS_FROM_FIRST_ACCESS_PREFIX . $postType,
            $fromDateFieldName = PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_FROM_DATE_PREFIX . $postType,
            $toDateFieldName = PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_TO_DATE_PREFIX . $postType
		);
	}
	
	static function cmacc_settings_after_save() {

		$postTypes = Settings::getPostTypesOptions();
		foreach ($postTypes as $postType => $label) {
			$roleOptionName = PostTypeRestriction::OPTION_POST_TYPE_ALLOWED_ROLES_PREFIX . $postType;
			$roleValue = (isset($_POST[$roleOptionName]) ? $_POST[$roleOptionName] : array());

			$allowedUsersOptionName = PostTypeRestriction::OPTION_POST_TYPE_ALLOWED_USERS_PREFIX . $postType;
			$notAllowedUsersOptionName = PostTypeRestriction::OPTION_POST_TYPE_NOT_ALLOWED_USERS_PREFIX . $postType;
			$allowedUsersValue = (isset($_POST[$allowedUsersOptionName]) ? $_POST[$allowedUsersOptionName] : array());
			$notAllowedUsersValue = (isset($_POST[$notAllowedUsersOptionName]) ? $_POST[$notAllowedUsersOptionName] : array());

			$restrictedResource = new PostTypeRestriction($postType);
			$restrictedResource->setAllowedRoles($roleValue);

			$restrictedResource->setAllowedUsers($allowedUsersValue);
			$restrictedResource->setNotAllowedUsers($notAllowedUsersValue);

			$daysOptionName = PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_DAYS_PREFIX . $postType;
			$daysValue      = (isset($_POST[$daysOptionName]) ? $_POST[$daysOptionName] : 0);
            $restrictedResource->setRestrictedDays($daysValue);

			$daysFromFirstAccessOptionName = PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_DAYS_FROM_FIRST_ACCESS_PREFIX . $postType;
			$daysFromFirstAccessValue      = (isset($_POST[$daysFromFirstAccessOptionName]) ? $_POST[$daysFromFirstAccessOptionName] : 0);
            $restrictedResource->setRestrictedDaysFromFirstAccess($daysFromFirstAccessValue);

			$fromDateOptionName = PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_FROM_DATE_PREFIX . $postType;
			$fromDateValue      = (isset($_POST[$fromDateOptionName]) ? $_POST[$fromDateOptionName] : date('Y-m-d'));
            $restrictedResource->setRestrictedFromDate($fromDateValue);

			$toDateOptionName = PostTypeRestriction::OPTION_POST_TYPE_RESTRICTED_TO_DATE_PREFIX . $postType;
			$toDateValue      = (isset($_POST[$toDateOptionName]) ? $_POST[$toDateOptionName] : date('Y-m-d'));
            $restrictedResource->setRestrictedToDate($toDateValue);
		}
	}

}