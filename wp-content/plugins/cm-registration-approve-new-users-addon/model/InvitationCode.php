<?php
namespace com\cminds\registration\addon\approvenewusers\model;

class InvitationCode extends PostType {
	
	const POST_TYPE = 'cmreg_invitcode';
	
	const META_APPROVE_NEW_USERS = 'cmreg_anu_approve_new_users';
	
	const APPROVE_STATUS_ENABLED = 'enabled';
	const APPROVE_STATUS_DISABLED = 'disabled';
	const APPROVE_STATUS_GLOBAL = 'global';
	
	static function registerPostType() {
		// don't
	}
	
	/**
	 * Get instance
	 *
	 * @param WP_Post|int $post Post object or ID
	 * @return com\cminds\registration\addon\approvenewusers\model\InvitationCode
	 */
	static function getInstance($post) {
		return parent::getInstance($post);
	}
	
	function isApprovingRequiredOrGlobal() {
		$val = $this->getApprovalStatus();
		if ($val == static::APPROVE_STATUS_GLOBAL) {
			return Settings::getOption(Settings::OPTION_APPROVE_REGISTRATION_ENABLE);
		} else {
			return ($val == static::APPROVE_STATUS_ENABLED);
		}
	}
	
	function getApprovalStatus() {
		$val = $this->getPostMeta(static::META_APPROVE_NEW_USERS);
		if (empty($val)) $val = static::APPROVE_STATUS_GLOBAL;
		return $val;
	}
	
	function setApprovalStatus($val) {
		return $this->setPostMeta(static::META_APPROVE_NEW_USERS, $val);
	}
	
	/**
	 * 
	 * @param unknown $userId
	 * @return com\cminds\registration\addon\approvenewusers\model\InvitationCode
	 */
	static function getByUser($userId) {
		global $wpdb;
		$sql = $wpdb->prepare("SELECT meta_value
				FROM $wpdb->usermeta
				WHERE meta_key = %s AND user_id = %d",
				User::META_INVITATION_CODE,
				$userId
				);
		$postId = $wpdb->get_var($sql);
		if ($postId) {
			return static::getInstance($postId);
		}
	}
	
}