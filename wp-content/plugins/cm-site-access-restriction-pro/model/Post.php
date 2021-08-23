<?php
namespace com\cminds\siteaccessrestriction\model;

class Post extends PostType {
	
	protected $post;
	
	static function registerPostType() {
		// don't
	}
	
	/**
	 * 
	 * @param unknown $postId
	 * @return Post
	 */
	static function getInstance($post) {
		return parent::getInstance($post);
	}

}