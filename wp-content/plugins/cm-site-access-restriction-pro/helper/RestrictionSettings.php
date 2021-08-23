<?php
namespace com\cminds\siteaccessrestriction\helper;

use com\cminds\siteaccessrestriction\controller\PostController;
use com\cminds\siteaccessrestriction\model\Settings;

class RestrictionSettings {
	
	static function displaySettings($restrictedResource, $restrictionFieldName, $roleFieldName, $allowedUsersFieldName, $notAllowedUsersFieldName, $daysFieldName, $daysFromFirstAccessFieldName, $fromDateFieldName, $toDateFieldName, $globalResource = null) {
		
		wp_enqueue_style('cmacc-backend');
		wp_enqueue_script('cmacc-backend');

		$rolesOptions = Settings::getRolesOptions();

		//$usersNicknames = Settings::getUsersNicknames();

		$restriction = ($restrictedResource ? $restrictedResource->getRestriction() : RestrictedResource::RESTRICTION_GLOBAL);
		$allowedRoles = ($restrictedResource ? $restrictedResource->getAllowedRoles() : array());

		$allowedUsers = ($restrictedResource ? $restrictedResource->getAllowedUsers() : array());
		$notAllowedUsers = ($restrictedResource ? $restrictedResource->getNotAllowedUsers() : array());

		$allowedUsersNicknames = Settings::getUsersNicknames($allowedUsers);
        $notAllowedUsersNicknames = Settings::getUsersNicknames($notAllowedUsers);

		$restrictedDays = ($restrictedResource ? $restrictedResource->getRestrictedDays() : 0);
		$restrictedDaysFromFirstAccess = ($restrictedResource ? $restrictedResource->getRestrictedDaysFromFirstAccess() : 0);
		$restrictedFromDate = ($restrictedResource ? $restrictedResource->getRestrictedFromDate() : 0);
		$restrictedToDate = ($restrictedResource ? $restrictedResource->getRestrictedToDate() : 0);
		$globalResource = ($globalResource ? $globalResource : ($restrictedResource ? $restrictedResource->getGlobalResource() : null));
		$currentMode = ($restrictedResource ? $restrictedResource->getRestrictedMode() : 'global');

		return PostController::loadBackendView('metabox', compact(
                'restrictedResource',
                'rolesOptions',
                'allowedUsersNicknames',
                'notAllowedUsersNicknames',
                'allowedUsersFieldName',
                'notAllowedUsersFieldName',
                'restrictionFieldName',
                'roleFieldName',
                'restriction',
                'allowedRoles',
                'allowedUsers',
                'notAllowedUsers',
                'globalResource',
                'restrictedDays',
                'restrictedDaysFromFirstAccess',
                'restrictedFromDate',
                'restrictedToDate',
                'daysFieldName',
                'daysFromFirstAccessFieldName',
                'fromDateFieldName',
                'toDateFieldName',
                'currentMode')
        );
	}
	
}