<?php
namespace com\cminds\registration\addon\approvenewusers\metabox;

use com\cminds\registration\addon\approvenewusers\model\InvitationCode;
use com\cminds\registration\addon\approvenewusers\controller\InvitationCodeController;

class InvitationCodeApproveNewUsersBox extends MetaBox {

	const SLUG = 'cmreg-approve-new-users';
	const NAME = 'Approve new users';
	//const META_BOX_PRIORITY = 5;
	const CONTEXT = 'side';
	const PRIORITY = 'low';

	const FIELD_APPROVE = 'cmreg_approve_new_users';
	
	static protected $supportedPostTypes = array(InvitationCode::POST_TYPE);
	
	static function render($post) {
		
		wp_enqueue_style('cmreganu-backend');
		wp_enqueue_script('cmreganu-backend');
		
		static::renderNonceField($post);
		
		$options = array(
			InvitationCode::APPROVE_STATUS_GLOBAL => 'follow global settings',
			InvitationCode::APPROVE_STATUS_ENABLED => 'enabled',
			InvitationCode::APPROVE_STATUS_DISABLED => 'disabled',
		);
		
		$code = InvitationCode::getInstance($post);
		echo InvitationCodeController::loadBackendView('metabox-approve-new-users', compact('code', 'options'));
	}
	
	static function savePost($postId) {
		if ($code = InvitationCode::getInstance($postId)) {
			$val = filter_input(INPUT_POST, static::FIELD_APPROVE);
			$code->setApprovalStatus($val);
		}
	}
	
}