<?php

namespace com\cminds\siteaccessrestriction\shortcode;

use com\cminds\siteaccessrestriction\App;

class Shortcode {
	
	const SHORTCODE_NAME = '';

	
	static function bootstrap() {
		add_action('init', array(get_called_class(), 'init'));
	}
	
	
	static function init() {
		add_shortcode( static::SHORTCODE_NAME, array(get_called_class(), 'shortcode') );
	}
	
	
	static function shortcode($atts) {
		return '';
	}
	
	
	static function wrap($code, $extra = '') {
		$name = strtolower(App::shortClassName(get_called_class(), 'Shortcode'));
		return sprintf('<div class="%s-widget %s-widget-%s"%s>%s</div>', App::PREFIX, App::PREFIX, esc_attr($name), $extra, $code);
	}
	
}
