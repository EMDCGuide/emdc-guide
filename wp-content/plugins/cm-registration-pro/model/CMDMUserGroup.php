<?php
namespace com\cminds\registration\model;

class CMDMUserGroup extends TaxonomyTerm {
	
	const TAXONOMY = 'cmdm_user_group';
	const USER_META_KEY = '_cmdm_group';
	
	/**
	 * Get list ttId => name
	 */
	static function getList() {
		$terms = CMDMUserGroup::getAll(CMDMUserGroup::FIELDS_ALL);
		$list = array();
		foreach ($terms as $term) {
			$list[$term->term_taxonomy_id] = $term->name;
		}
		return $list;
	}
	
	static function addUserToGroup($userId, $groupTTId) {
		return add_user_meta($userId, static::USER_META_KEY, $groupTTId, $unique = false);
	}
	
}