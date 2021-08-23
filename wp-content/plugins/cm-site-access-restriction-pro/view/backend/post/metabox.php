<?php
use com\cminds\siteaccessrestriction\metabox\PostAccessBox;
use com\cminds\siteaccessrestriction\helper\FormHtml;
use com\cminds\siteaccessrestriction\model\Settings;
use com\cminds\siteaccessrestriction\helper\PostRestriction;
?>
<div class="cmacc-restriction-settings">
    
	<div class="access_not_restricted_con">
		<?php echo FormHtml::radioWithLabel($restrictionFieldName, PostRestriction::RESTRICTION_NONE, PostRestriction::$restrictionNames[PostRestriction::RESTRICTION_NONE], $restriction); ?>
	</div>

    <div class="access_restricted_to_logged_in_users_con">
		<?php echo FormHtml::radioWithLabel($restrictionFieldName, PostRestriction::RESTRICTION_LOGGED_IN_USERS, PostRestriction::$restrictionNames[PostRestriction::RESTRICTION_LOGGED_IN_USERS], $restriction); ?>
		<div class="cmacc-checkboxlist-container cmacc-restrict-loggedin"<?php if ($restriction != PostRestriction::RESTRICTION_LOGGED_IN_USERS) echo ' style="display:none"'; ?>>
			<?php echo FormHtml::inputWithLabel($daysFromFirstAccessFieldName, 'number', $restrictedDaysFromFirstAccess, 'Access denied X days from user first access'); ?>
        </div>
	</div>
    
	<div class="allow_only_anonymous_access_con">
		<?php echo FormHtml::radioWithLabel($restrictionFieldName, PostRestriction::RESTRICTION_GUESTS, PostRestriction::$restrictionNames[PostRestriction::RESTRICTION_GUESTS], $restriction); ?>
	</div>
    
	<div class="access_restricted_by_role_con">
        <?php echo FormHtml::radioWithLabel($restrictionFieldName, PostRestriction::RESTRICTION_ROLES, PostRestriction::$restrictionNames[PostRestriction::RESTRICTION_ROLES], $restriction); ?>
        <div class="cmacc-checkboxlist-container cmacc-restrict-roles"<?php if ($restriction != PostRestriction::RESTRICTION_ROLES) echo ' style="display:none"'; ?>>
            <?php echo FormHtml::checkboxList($roleFieldName . '[]', $rolesOptions, $allowedRoles); ?>
        </div>
    </div>

    <div class="access_restricted_by_whitelist_con">
        <?php echo FormHtml::radioWithLabel($restrictionFieldName, PostRestriction::RESTRICTION_WHITELIST, PostRestriction::$restrictionNames[PostRestriction::RESTRICTION_WHITELIST], $restriction); ?>
        <div class="cmacc-checkboxlist-container cmacc-restrict-whitelist"<?php if ($restriction != PostRestriction::RESTRICTION_WHITELIST) echo ' style="display:none"'; ?>>
            <?php echo FormHtml::userInputWithLabel('Search User ', $allowedUsersFieldName); ?>
            <?php echo FormHtml::checkboxList($allowedUsersFieldName . '[]', $allowedUsersNicknames, $allowedUsers); ?>
        </div>
    </div>

    <div class="access_restricted_by_blacklist_con">
        <?php echo FormHtml::radioWithLabel($restrictionFieldName, PostRestriction::RESTRICTION_BLACKLIST, PostRestriction::$restrictionNames[PostRestriction::RESTRICTION_BLACKLIST], $restriction); ?>
        <div class="cmacc-checkboxlist-container cmacc-restrict-blacklist"<?php if ($restriction != PostRestriction::RESTRICTION_BLACKLIST) echo ' style="display:none"'; ?>>
            <?php echo FormHtml::userInputWithLabel('Search User ', $notAllowedUsersFieldName); ?>
            <?php echo FormHtml::checkboxList($notAllowedUsersFieldName . '[]', $notAllowedUsersNicknames, $notAllowedUsers); ?>
        </div>
    </div>

	<div class="access_restricted_by_specificdate_con">
        <?php echo FormHtml::radioWithLabel($restrictionFieldName, PostRestriction::RESTRICTION_SPECIFICDATE, PostRestriction::$restrictionNames[PostRestriction::RESTRICTION_SPECIFICDATE], $restriction); ?>
        <div class="cmacc-checkboxlist-container cmacc-restrict-specificdate"<?php if ($restriction != PostRestriction::RESTRICTION_SPECIFICDATE) echo ' style="display:none"'; ?>>
            <label>
			<span class="from"><span>From</span> <input type="date" name="<?php echo $fromDateFieldName; ?>" value="<?php echo $restrictedFromDate; ?>" /></span>
			<span class="to"><span>To</span> <input type="date" name="<?php echo $toDateFieldName; ?>" value="<?php echo $restrictedToDate; ?>" /></span>
			</label>
        </div>
    </div>
    
	<?php
	if($globalResource) {
		$label = PostRestriction::$restrictionNames[PostRestriction::RESTRICTION_GLOBAL] . ': <strong>' . $globalResource->getRestrictionLabel() .'</strong>';
		?>
        <div class="follow_access_global_options_con">
			<?php echo FormHtml::radioWithLabel($restrictionFieldName, PostRestriction::RESTRICTION_GLOBAL, $label, $restriction); ?>
		</div>
		<?php
	}
	?>

    <div class="access_denied_x_days_from_registration_con">
        <?php echo FormHtml::inputWithLabel($daysFieldName, 'number', $restrictedDays, 'Access denied X days from registration'); ?>
    </div>
	
	<div class="restriction_type_con">
		<label>Restriction type<br>
			<?php
			$restrictModeOptions = array('global' => 'Global settings') + Settings::getRestrictType();
			echo FormHtml::selectBox('cmacc-mode', $restrictModeOptions, $currentMode);
			?>
		</label>
	</div>

</div>