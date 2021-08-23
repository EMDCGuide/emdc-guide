<?php
namespace com\cminds\registration\addon\approvenewusers\controller;

use com\cminds\registration\addon\approvenewusers\model\InvitationCode;
use com\cminds\registration\addon\approvenewusers\model\Settings;
use com\cminds\registration\addon\approvenewusers\model\User;
use com\cminds\registration\addon\approvenewusers\model\Labels;
use com\cminds\registration\addon\approvenewusers\lib\Email;
use com\cminds\registration\addon\approvenewusers\controller\abstracts\ValidLicenseController;

class RegistrationController extends ValidLicenseController {
	
	const PARAM_ADMIN_APPROVE = 'cmreg_anu_action';
	const PARAM_USER_KEY = 'cmreg_anu_user_key';
	
	const ACTION_ADMIN_APPROVE = 'admin_approve';
	const ACTION_ADMIN_REJECT = 'admin_reject';
	
	static $actions = array(
		'init',
		'register_new_user' => array('args' => 1, 'priority' => 500),
	);
	static $filters = array(
		'cmreg_registration_ajax_response' => array('args' => 2, 'priority' => 100000),
	);

	static function init() {
		if ($action = filter_input(INPUT_GET, static::PARAM_ADMIN_APPROVE) AND $userKey = filter_input(INPUT_GET, static::PARAM_USER_KEY)
				AND $user = User::getByApprovalKey($userKey)) {
			switch ($action) {
				case static::ACTION_ADMIN_APPROVE:
					$user->setApprovalStatus(User::APPROVAL_STATUS_APPROVED);
					static::sendUserApprovedNotification($user);
					static::initAdminMessage(Labels::getLocalized('admin_user_approved'));
					break;
				case static::ACTION_ADMIN_REJECT:
					static::sendUserRejectedNotification($user);
					$user->delete();
					static::initAdminMessage(Labels::getLocalized('admin_user_rejected'));
					break;
			}
		}
	}
	
	protected static function initAdminMessage($msg) {

		add_action('wp_enqueue_scripts', function() {
			wp_enqueue_script('cmreganu-account-approval');
		});
		wp_localize_script('cmreganu-account-approval', 'cmreganu_account_approval', compact('msg'));
		
		if(is_user_logged_in() && current_user_can('administrator')) {
			wp_redirect(site_url('/wp-admin/users.php'));
			exit;
		}

	}

	/**
	 * After successful registration
	 *
	 * @param unknown $userId
	 */
	static function register_new_user($userId) {
		
		$user = User::getInstance($userId);
		
		$auto_approve_invitation_code = Settings::getOption(Settings::OPTION_AUTO_APPROVE_INVITATION_CODE);

		$auto_approve_domains_array = array();
		$auto_approve_domains = trim(Settings::getOption(Settings::OPTION_AUTO_APPROVE_DOMAINS));
		if (!empty($auto_approve_domains)) {
			$auto_approve_domains = array_map('trim', explode("\n", $auto_approve_domains));
			$auto_approve_domains_array = array_values(array_filter($auto_approve_domains));
		}

		if ($code = InvitationCode::getByUser($userId)) {
			$approvalRequired = $code->isApprovingRequiredOrGlobal();
			$invitationCodeId = $code->getId();
		} else {
			$approvalRequired = Settings::getOption(Settings::OPTION_APPROVE_REGISTRATION_ENABLE);
			$invitationCodeId = null;
		}
		
		if($auto_approve_invitation_code == '1' && $invitationCodeId != null) {
			$approvalRequired = 0;
		}

		if(count($auto_approve_domains_array) > 0) {
			$user_email = trim($user->getEmail());
			$user_email_array = explode("@", $user_email);
			$user_email_domain = $user_email_array[1];
			if(in_array($user_email_domain, $auto_approve_domains_array)) {
				$approvalRequired = 0;
			}
		}

		$user->setApprovalStatus($approvalRequired ? User::APPROVAL_STATUS_PENDING : User::APPROVAL_STATUS_DISABLED);
		
		if ($approvalRequired) {
			$user->generateApprovalKey();
			static::sendAdminNotification($user);

			$approvalOwnerRequired = Settings::getOption(Settings::OPTION_OWNER_APPROVE_REGISTRATION_ENABLE);
			if ($approvalOwnerRequired) {
				static::sendOwnerAdminNotification($user, $invitationCodeId);
			}
		}
	}

	static function cmreg_registration_ajax_response($response, $userId) {
		if ($userId AND !empty($response['success']) AND $user = User::getInstance($userId) AND User::APPROVAL_STATUS_PENDING == $user->getApprovalStatus()) {
			//if (empty($response['msg'])) $response['msg'] = '';
			$response['msg'] = Labels::getLocalized('registration_success_msg_approval_needed');
			//$response['redirect'] = PaymentController::getPaymentUrl($user);
			User::logout();
		}
		return $response;
	}
	
	protected static function sendOwnerAdminNotification(User $user, $invitation_code_id) {
		$email = '';
		if($invitation_code_id)
		{
			//$invitation_code = get_post_meta($invitation_code_id, 'cmreg_code_string', true);
			$post = get_post($invitation_code_id);
			$post_title = $post->post_title;
			if($post_title!='')
			{
				$post_title_arr = explode(' for ', $post_title);
				if($post_title_arr[1] && trim($post_title_arr[1]) != '')
				$email = trim($post_title_arr[1]);
			}
		}

		if (empty($email)) return;
			
		$approveUrl = $user->getAccountApproveUrl();
		$rejectUrl = $user->getAccountRejectUrl();

		$vars = Email::getBlogVars() + Email::getUserVars($user->getId()) + array('[approveurl]' => $approveUrl, '[rejecturl]' => $rejectUrl);
		$subject = Settings::getOption(Settings::OPTION_NEW_USER_OWNER_ADMIN_NOTIF_EMAIL_SUBJECT);
		$body = Settings::getOption(Settings::OPTION_NEW_USER_OWNER_ADMIN_NOTIF_EMAIL_BODY);
		$body = strtr($body, array("\n" => '<br>'));
		Email::send($email, $subject, $body, $vars, array('Content-type: text/html'));
	}

	protected static function sendAdminNotification(User $user) {
		
		$mode = false;
		$profile_field_key = Settings::getOption(Settings::OPTION_APPROVE_REGISTRATION_PROFILE_FIELD);
		if($profile_field_key != '') {
			$profile_field_email = trim(get_user_meta($user->getId(), $profile_field_key, true));
			if($profile_field_email != '') {
				$cuser = get_user_by('email', $profile_field_email);
				if($cuser) {
					$status = get_user_meta($cuser->ID, 'cmreganu_approval_status', true);
					if($status == '' || $status == 'approved') {
						$mode = true;
						$adminemails_array = array($profile_field_email);
					}
				}
			}
		}

		if($mode == false) {
			$adminemails_array = Settings::getOption(Settings::OPTION_NEW_USER_ADMIN_NOTIF_EMAILS_LIST);
		}
		
		if(count($adminemails_array) > 0) {
			$approveUrl = $user->getAccountApproveUrl();
			$rejectUrl = $user->getAccountRejectUrl();
			$vars = Email::getBlogVars() + Email::getUserVars($user->getId()) + array('[approveurl]' => $approveUrl, '[rejecturl]' => $rejectUrl);
			$subject = Settings::getOption(Settings::OPTION_NEW_USER_ADMIN_NOTIF_EMAIL_SUBJECT);
			$body = Settings::getOption(Settings::OPTION_NEW_USER_ADMIN_NOTIF_EMAIL_BODY);
			$body = strtr($body, array("\n" => '<br>'));
			foreach($adminemails_array as $adminemail) {
				Email::send(trim($adminemail), $subject, $body, $vars, array('Content-type: text/html'));
			}
		}
	}
	
	protected static function sendUserApprovedNotification(User $user) {
		$subject = Settings::getOption(Settings::OPTION_ACCOUNT_APPROVED_EMAIL_SUBJECT);
		$body = Settings::getOption(Settings::OPTION_ACCOUNT_APPROVED_EMAIL_BODY);
		if(get_user_meta($user->getId(), 'cmreganu_approval_email', true) == '1') {
			
		} else {
			update_user_meta($user->getId(), 'cmreganu_approval_email', '1');
			static::sendUserNotification($user, $subject, $body);
		}
	}
	
	protected static function sendUserRejectedNotification(User $user) {
		$subject = Settings::getOption(Settings::OPTION_ACCOUNT_REJECTED_EMAIL_SUBJECT);
		$body = Settings::getOption(Settings::OPTION_ACCOUNT_REJECTED_EMAIL_BODY);
		if(get_user_meta($user->getId(), 'cmreganu_reject_email', true) == '1') {
			
		} else {
			update_user_meta($user->getId(), 'cmreganu_reject_email', '1');
			static::sendUserNotification($user, $subject, $body);
		}
	}
	
	protected static function sendUserNotification(User $user, $subject, $body) {
		$vars = Email::getBlogVars() + Email::getUserVars($user->getId());
		$body = strtr($body, array("\n" => '<br>', "\r" => '<br>'));
		Email::send($user->getEmail(), $subject, $body, $vars, array('Content-type: text/html'));
	}

}