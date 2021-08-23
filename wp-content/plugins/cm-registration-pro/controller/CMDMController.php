<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\App;
use com\cminds\registration\model\InvitationCode;
use com\cminds\registration\model\CMDMUserGroup;

class CMDMController extends Controller {
	
	static $actions = array(
		'cmreg_invitation_code_edit' => array('args' => 2),
		'cmreg_invitation_code_save' => array('args' => 2),
		'register_new_user' => array('args' => 1, 'priority' => 1000000),
	);
	
	static function cmreg_invitation_code_edit($postId, InvitationCode $code) {
		if (static::isCMDMAvailable()) {
			$groups = CMDMUserGroup::getList();
			echo static::loadBackendView('invitation-code-edit-metabox', compact('code', 'groups'));
		}
	}
	
	static function cmreg_invitation_code_save($postId, InvitationCode $code) {
		if (static::isCMDMAvailable()) {
			if (isset($_POST[InvitationCode::META_CMDM_USER_GROUP])) {
				$code->setCMDMUserGroup($_POST[InvitationCode::META_CMDM_USER_GROUP]);
			}
		}
	}
	
	/**
	 * After successful registration
	 *
	 * @param int $userId
	 */
	static function register_new_user($userId) {
		if (!App::isLicenseOk()) return;
		if (!static::isCMDMAvailable()) return;
		
		$code = InvitationCode::getByUser($userId);
		if ($code) {
			$groupTTId = $code->getCMDMUserGroup();
			if ($groupTTId) {
				$r = CMDMUserGroup::addUserToGroup($userId, $groupTTId);
			}
		}
	}
	
	static function isCMDMAvailable() {
		return class_exists('\CMDM_Settings');
	}
	
	static function getViewPath($_viewName, $prefixDir = '') {
		$dir = 'cmdm';
		return $prefixDir . $dir . DIRECTORY_SEPARATOR . $_viewName;
	}
	
}