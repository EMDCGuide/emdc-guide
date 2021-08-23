<?php

namespace com\cminds\siteaccessrestriction\model;

abstract class Model {
	
	
	static function bootstrap() {
		add_action('init', array(get_called_class(), 'init'), 2);
	}
	
	static function init() {}
	
}
