<?php
namespace com\cminds\registration\addon\approvenewusers\model;

use com\cminds\registration\addon\approvenewusers\controller\RegistrationController;

class User extends Model {
	
	const META_INVITATION_CODE = 'cmreg_invitation_code';
	const META_EMAIL_VERIFICATION_STATUS = 'cmreg_email_verification_status';
	const META_EMAIL_VERIFICATION_CODE = 'cmreg_email_verification_code';
	
	const META_APPROVAL_STATUS = 'cmreganu_approval_status';
	const META_APPROVAL_KEY = 'cmreganu_approval_key';
	
	const APPROVAL_STATUS_DISABLED = 'disabled';
	const APPROVAL_STATUS_PENDING = 'pending';
	const APPROVAL_STATUS_APPROVED = 'approved';
	const APPROVAL_STATUS_REJECTED = 'rejected';
	
	const DELETE_PENDING_USERS_DAYS = 365;
	
	/**
	 * 
	 * @var \WP_user
	 */
	protected $user;
	
	static function getInstance($user) {
		if (is_scalar($user)) {
			$user = get_userdata($user);
		}
		if (is_object($user) AND $user instanceof \WP_User) {
			return new static($user);
		}
	}
	
	function __construct(\WP_User $user) {
		$this->user = $user;
	}
	
	function getId() {
		return $this->user->ID;
	}
	
	function getApprovalStatus() {
		return get_user_meta($this->getId(), static::META_APPROVAL_STATUS, $single = true);
	}
	
	function setApprovalStatus($status) {
		return update_user_meta($this->getId(), static::META_APPROVAL_STATUS, $status);
	}
	
	function generateApprovalKey() {
		$key = sha1($this->getId() . mt_rand() . $this->getEmail());
		$this->setApprovalKey($key);
		return $key;
	}
	
	function setApprovalKey($key) {
		return update_user_meta($this->getId(), static::META_APPROVAL_KEY, $key);
	}
	
	function getApprovalKey() {
		return get_user_meta($this->getId(), static::META_APPROVAL_KEY, $single = true);
	}
	
	function getInvitationCodeId() {
		return get_user_meta($this->getId(), static::META_INVITATION_CODE, $single = true);
	}
	
	function isApprovalNeeded() {
		$status = $this->getApprovalStatus();
		switch ($status) {
			case static::APPROVAL_STATUS_PENDING:
			case static::APPROVAL_STATUS_REJECTED:
				return true;
			case static::APPROVAL_STATUS_DISABLED:
			case static::APPROVAL_STATUS_APPROVED:
			default:
				return false;
		}
	}
	
	static function logout() {
		wp_destroy_current_session();
		wp_clear_auth_cookie();
	}
	
	static function getByApprovalKey($key) {
		global $wpdb;
		$userId = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value = %s", static::META_APPROVAL_KEY, $key));
		if ($userId) {
			return static::getInstance($userId);
		}
	}
	
	static function deleteInactiveUsers() {
		global $wpdb;
		$days = static::DELETE_PENDING_USERS_DAYS;
		if ($days > 0) {
			
			$timestamp = time() - $days * 3600 * 24;
			$date = Date('Y-m-d H:i:s', $timestamp);
			
			$usersIds = $wpdb->get_col($wpdb->prepare("SELECT u.ID FROM $wpdb->users u
				JOIN $wpdb->usermeta ms ON ms.user_id = u.ID AND ms.meta_key = %s AND ms.meta_value = %s
				WHERE u.user_registered < %s",
				self::META_APPROVAL_STATUS,
				self::APPROVAL_STATUS_PENDING,
				$date
			));
			
			require_once(ABSPATH.'wp-admin/includes/user.php' );
			foreach ($usersIds as $id) {
				$res = \wp_delete_user($id);
			}
			
		}
	}
	
	static function selectPendingUsers() {
		global $wpdb;
		
		$usersIds = $wpdb->get_col($wpdb->prepare("SELECT u.ID FROM $wpdb->users u
			JOIN $wpdb->usermeta ms ON ms.user_id = u.ID AND ms.meta_key = %s AND ms.meta_value = %s
			ORDER BY user_registered ASC",
			self::META_APPROVAL_STATUS,
			self::APPROVAL_STATUS_PENDING
		));
		
		return array_filter(array_map(function($userId) {
			return User::getInstance($userId);
		}, $usersIds));
		
	}
	
	function getEmail() {
		return $this->user->user_email;
	}
	
	function getLogin() {
		return $this->user->user_login;
	}
	
	function getDisplayName() {
		return $this->user->display_name;
	}
	
	function getRegistrationDate() {
		return $this->user->user_registered;
	}
	
	function getEditUrl() {
		return add_query_arg(array(
			'user_id' => $this->getId(),
		), admin_url('user-edit.php'));
	}
	
	function getAccountApproveUrl($url = null) {
		if (empty($url)) $url = site_url();
		return add_query_arg(array(
			RegistrationController::PARAM_ADMIN_APPROVE => RegistrationController::ACTION_ADMIN_APPROVE,
			RegistrationController::PARAM_USER_KEY => $this->getApprovalKey(),
		), $url);
	}
	
	function getAccountRejectUrl($url = null) {
		if (empty($url)) $url = site_url();
		return add_query_arg(array(
			RegistrationController::PARAM_ADMIN_APPROVE => RegistrationController::ACTION_ADMIN_REJECT,
			RegistrationController::PARAM_USER_KEY => $this->getApprovalKey(),
		), $url);
	}
	
	function delete() {
		if (!function_exists('\\wp_delete_user')) {
			require_once(ABSPATH.'wp-admin/includes/user.php' );
		}
		\wp_delete_user($this->getId());
	}

}