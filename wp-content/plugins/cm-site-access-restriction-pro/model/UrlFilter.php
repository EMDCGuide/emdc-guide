<?php
namespace com\cminds\siteaccessrestriction\model;

use com\cminds\siteaccessrestriction\controller\UrlController;

class UrlFilter extends Model {
	
	const OPTION_URL_FILTERS = 'cmacc_url_filters';
	
	protected $filter;
	
	function __construct($filter) {
		$this->filter = $filter;
	}
	
	function getId() {
		return $this->filter['id'];
	}
	
	function getUrl() {
		return $this->filter['url'];
	}
	
	function getRaw() {
		return $this->filter;
	}
	
	static function getAllRaw() {
		return get_option(static::OPTION_URL_FILTERS, array());
	}
	
	static function getAllInstances() {
		$raw = static::getAllRaw();
		$out = array();
		foreach ($raw as $id => $filter) {
			$out[$id] = new static($filter);
		}
		return $out;
	}
	
	static function saveAllInstances(array $filters) {
		$raw = array();
		foreach ($filters as $filter) {
			$raw[$filter->getId()] = $filter->getRaw();
		}
		return static::saveAllRaw($raw);
	}
	
	static function saveAllRaw(array $raw) {
		return update_option(static::OPTION_URL_FILTERS, $raw, $autoload = true);
	}
	
	static function create($url) {
		$id = mt_rand();
		$filters = static::getAllRaw();
		$filters[$id] = array('url' => $url);
		static::saveAllRaw($filters);
		return $id;
	}
	
	function getDeleteUrl() {
		return add_query_arg(array(
			'page' => UrlController::getMenuSlug(),
			UrlController::PARAM_ACTION => UrlController::ACTION_DELETE,
			'nonce' => wp_create_nonce(UrlController::ACTION_DELETE),
			'id' => $this->getId(),
		), admin_url('admin.php'));
	}
	
	function matchUrl($url) {
		$exp = $this->getUrl();
		if (strpos($exp, '~') === 0 AND strrpos($exp, '~') === strlen($exp)-1) {
			// Regular expression
			return preg_match($exp, $url);
		} else {
			if ($exp === $url) {
				return true;
			} else {
				
				// Contains wildcard
				if (strpos($exp, '*') !== false) {
					$exp = '~' . str_replace('__WILDCARD__', '.*', preg_quote(str_replace('*', '__WILDCARD__', $exp), '~')) . '~';
					return preg_match($exp, $url);
				}
				
			}
		}
	}
	
	static function getInstanceByUrl($url) {
		$filters = static::getAllInstances();
		foreach ($filters as $filter) {
			if ($filter->matchUrl($url)) {
				return $filter;
			}
		}
	}
	
	static function deleteById($id) {
		$filters = UrlFilter::getAllRaw();
		$key = null;
		foreach ($filters as $k => $filter) {
			if ($filter['id'] == $id) {
				$key = $k;
			}
		}
		if (!empty($key)) unset($filters[$key]);
		self::saveAllRaw($filters);
	}
	
}