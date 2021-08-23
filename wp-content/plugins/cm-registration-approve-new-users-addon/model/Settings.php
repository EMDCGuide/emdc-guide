<?php
namespace com\cminds\registration\addon\approvenewusers\model;

use com\cminds\registration\addon\approvenewusers\lib\Email;
use com\cminds\registration\addon\approvenewusers\App;

class Settings extends SettingsAbstract {
	
	const OPTION_APPROVE_REGISTRATION_ENABLE = 'cmreg_anu_approve_registration_enable';
	const OPTION_APPROVE_REGISTRATION_PROFILE_FIELD = 'cmreg_anu_approve_registration_by_profile_field';

	const OPTION_NEW_USER_ADMIN_NOTIF_EMAILS_LIST = 'cmreg_anu_new_user_admin_notif_emails_list';
	const OPTION_NEW_USER_ADMIN_NOTIF_EMAIL_SUBJECT = 'cmreg_anu_new_user_admin_notif_subject';
	const OPTION_NEW_USER_ADMIN_NOTIF_EMAIL_BODY = 'cmreg_anu_new_user_admin_notif_body';

	const OPTION_OWNER_APPROVE_REGISTRATION_ENABLE = 'cmreg_anu_owner_approve_registration_enable';
	const OPTION_NEW_USER_OWNER_ADMIN_NOTIF_EMAIL_SUBJECT = 'cmreg_anu_new_user_owner_admin_notif_subject';
	const OPTION_NEW_USER_OWNER_ADMIN_NOTIF_EMAIL_BODY = 'cmreg_anu_new_user_owner_admin_notif_body';

	const OPTION_ACCOUNT_APPROVED_EMAIL_SUBJECT = 'cmreg_anu_user_approved_email_subject';
	const OPTION_ACCOUNT_APPROVED_EMAIL_BODY = 'cmreg_anu_user_approved_email_body';
	const OPTION_ACCOUNT_REJECTED_EMAIL_SUBJECT = 'cmreg_anu_user_rejected_email_subject';
	const OPTION_ACCOUNT_REJECTED_EMAIL_BODY = 'cmreg_anu_user_rejected_email_body';

	const OPTION_AUTO_APPROVE_DOMAINS = 'cmreg_anu_auto_approve_domains';
	const OPTION_AUTO_APPROVE_INVITATION_CODE = 'cmreg_anu_auto_approve_invitation_code';
	
	const SETTINGS_CATEGORY_KEY = 'approve_new_users';
	const SETTINGS_SUBCATEGORY_GENERAL = 'general';
	const SETTINGS_SUBCATEGORY_ADMIN_NOTIFICATIONS = 'admin_notifications';
	const SETTINGS_SUBCATEGORY_OWNER_ADMIN_NOTIFICATIONS = 'owner_admin_notifications';
	const SETTINGS_SUBCATEGORY_USER_NOTIFICATIONS = 'user_notifications';
	const SETTINGS_SUBCATEGORY_AUTO_APPROVE_DOMAINS = 'auto_approve_domains';
	
	public static function getOptionsConfig() {
		$className = 'com\cminds\registration\model\Settings';
		if (class_exists($className)) {
			return call_user_func(array($className, 'getOptionsConfig'));
		} else {
			return array();
		}
	}
	
	public static function processPostRequest($data) {
		// do nothing
	}
	
	static function getAddonOptionsConfig() {
		return array(
			
			// General
			static::OPTION_APPROVE_REGISTRATION_ENABLE => array(
				'type' => static::TYPE_BOOL,
				'default' => 0,
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_GENERAL,
				'title' => 'Enable approving new users by admin',
			),	
			static::OPTION_APPROVE_REGISTRATION_PROFILE_FIELD => array(
				'type' => static::TYPE_STRING,
				'default' => '',
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_GENERAL,
				'title' => 'Enable approving new users by profile field',
				'desc' => 'Enter "User Meta Key" from profile fields section',
			),	
			// Admin Notifications
			static::OPTION_NEW_USER_ADMIN_NOTIF_EMAILS_LIST => array(
				'type' => static::TYPE_CSV_LINE,
				'default' => get_bloginfo('admin_email'),
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_ADMIN_NOTIFICATIONS,
				'title' => 'Email addresses list to send the notification to',
				'desc' => 'Comma separated emails list.',
			),
			static::OPTION_NEW_USER_ADMIN_NOTIF_EMAIL_SUBJECT => array(
				'type' => static::TYPE_STRING,
				'default' => '[[blogname]] Registered new user: [userlogin]',
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_ADMIN_NOTIFICATIONS,
				'title' => 'Subject of the notification email',
				'desc' => 'This email will be send to the administrators\'s emails list directly after the new user has registered his account.'
						. ' You can use the following shortcodes: [blogname] [siteurl] [userlogin] [useremail] [userdisplayname] [userrole] [userfirstname] [userlastname]',
			),
			static::OPTION_NEW_USER_ADMIN_NOTIF_EMAIL_BODY => array(
				'type' => static::TYPE_RICH_TEXT,
				'default' => 'New user has registered on your website <a href="[siteurl]">[blogname]</a><br><br>'
					. 'User login: [userlogin]<br>User email: [useremail]<br>User role: [userrole]<br><br>'
					. 'You can: <a href="[approveurl]">APPROVE</a> or <a href="[rejecturl]">REJECT</a> that registration',
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_ADMIN_NOTIFICATIONS,
				'title' => 'Template of the notification email',
				'desc' => 'You can use the following shortcodes: [blogname] [siteurl] [userlogin] [useremail] [userdisplayname] [userrole] [userfirstname] [userlastname]'
						. ' [rejecturl] [approveurl]',
			),

			//  Owner Notifications
			static::OPTION_OWNER_APPROVE_REGISTRATION_ENABLE => array(
				'type' => static::TYPE_BOOL,
				'default' => 0,
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_OWNER_ADMIN_NOTIFICATIONS,
				'title' => 'Enable approving new user by owner',
			),
			static::OPTION_NEW_USER_OWNER_ADMIN_NOTIF_EMAIL_SUBJECT => array(
				'type' => static::TYPE_STRING,
				'default' => '[[blogname]] Registered new user: [userlogin]',
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_OWNER_ADMIN_NOTIFICATIONS,
				'title' => 'Subject of the notification email for owner',
				'desc' => 'This email will be send to the owner\'s emails list directly after the new user has registered his account.'
						. ' You can use the following shortcodes: [blogname] [siteurl] [userlogin] [useremail] [userdisplayname] [userrole] [userfirstname] [userlastname]',
			),
			static::OPTION_NEW_USER_OWNER_ADMIN_NOTIF_EMAIL_BODY => array(
				'type' => static::TYPE_RICH_TEXT,
				'default' => 'New user has registered on your website <a href="[siteurl]">[blogname]</a><br><br>'
					. 'User login: [userlogin]<br>User email: [useremail]<br>User role: [userrole]<br><br>'
					. 'You can: <a href="[approveurl]">APPROVE</a> or <a href="[rejecturl]">REJECT</a> that registration',
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_OWNER_ADMIN_NOTIFICATIONS,
				'title' => 'Template of the notification email for owner',
				'desc' => 'You can use the following shortcodes: [blogname] [siteurl] [userlogin] [useremail] [userdisplayname] [userrole] [userfirstname] [userlastname]'
						. ' [rejecturl] [approveurl]',
			),
			
			// User Notifications
			static::OPTION_ACCOUNT_APPROVED_EMAIL_SUBJECT => array(
				'type' => static::TYPE_STRING,
				'default' => '[[blogname]] Your account has been approved and you can now login',
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_USER_NOTIFICATIONS,
				'title' => 'Subject of the notification email after approved new account',
				'desc' => 'This email will be send to the user after the administrator approved his account.'
						. 'You can use the following shortcodes: [blogname] [siteurl] [userlogin] [useremail] [userdisplayname] [userfirstname] [userlastname] [wploginurl]',
			),
			static::OPTION_ACCOUNT_APPROVED_EMAIL_BODY => array(
				'type' => static::TYPE_RICH_TEXT,
				'default' => 'Hi [userdisplayname]<br>You account has been approved by the administrator.<br><br><a href="[wploginurl]">Login to [blogname]</a>',
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_USER_NOTIFICATIONS,
				'title' => 'Template of the notification email after approved new account',
				'desc' => 'You can use the following shortcodes: [blogname] [siteurl] [userlogin] [useremail] [userdisplayname] [userfirstname] [userlastname] [wploginurl]',
			),
			static::OPTION_ACCOUNT_REJECTED_EMAIL_SUBJECT => array(
				'type' => static::TYPE_STRING,
				'default' => '[[blogname]] Your account has been rejected',
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_USER_NOTIFICATIONS,
				'title' => 'Subject of the notification email after rejected new account',
				'desc' => 'This email will be send to the user after the administrator rejected his account.'
				. 'You can use the following shortcodes: [blogname] [siteurl] [userlogin] [useremail] [userdisplayname] [userfirstname] [userlastname]',
			),
			static::OPTION_ACCOUNT_REJECTED_EMAIL_BODY => array(
				'type' => static::TYPE_RICH_TEXT,
				'default' => 'Hi [userdisplayname]<br>You account has been rejected by the administrator.',
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_USER_NOTIFICATIONS,
				'title' => 'Template of the notification email after rejected new account',
				'desc' => 'You can use the following shortcodes: [blogname] [siteurl] [userlogin] [useremail] [userdisplayname] [userfirstname] [userlastname]',
			),
			static::OPTION_AUTO_APPROVE_INVITATION_CODE => array(
				'type' => static::TYPE_BOOL,
				'default' => 0,
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_AUTO_APPROVE_DOMAINS,
				'title' => 'Allow by invitation code',
			),
			static::OPTION_AUTO_APPROVE_DOMAINS => array(
				'type' => static::TYPE_TEXTAREA,
				'default' => '',
				'category' => static::SETTINGS_CATEGORY_KEY,
				'subcategory' => static::SETTINGS_SUBCATEGORY_AUTO_APPROVE_DOMAINS,
				'title' => 'Allow by domains',
				'desc' => 'Separate domain name by new lines.<br>Examples:<br><kbd>gmail.com</kbd><br><kbd>yahoo.com</kbd><br><kbd>yahoo.co.in</kbd>'
					. '<br><br>Your domain address is: <kbd>'. $_SERVER['HTTP_HOST'] .'</kbd>',
			),
				
		);
	}
	
}