<?php
namespace com\cminds\siteaccessrestriction\controller;

use com\cminds\siteaccessrestriction\helper\UrlRestriction;
use com\cminds\siteaccessrestriction\model\UrlFilter;
use com\cminds\siteaccessrestriction\helper\PostTypeRestriction;
use com\cminds\siteaccessrestriction\helper\PostRestriction;
use com\cminds\siteaccessrestriction\model\Settings;
use com\cminds\siteaccessrestriction\model\Post;
use com\cminds\siteaccessrestriction\App;

class UrlController extends Controller {

    const ACTION_SAVE = 'cmacc-url-filters-save';
    const ACTION_DELETE = 'cmacc-url-filter-delete';
    const PARAM_ACTION = 'cmacc-action';

    static $actions = array(
        'admin_menu' => array('priority' => 15),
        'template_redirect',
    );

    static $filters = array(
        'the_content' => array('priority' => PHP_INT_MAX),
    );

    /**
     * Redirects from the restricted post if post is restricted and the URL has been defined in settings.
     */
    static function template_redirect() {
        if (App::isLicenseOk() AND ! is_admin()) {
            if ($filter = UrlFilter::getInstanceByUrl($_SERVER['REQUEST_URI'])) {
                $urlRestriction = new UrlRestriction($filter);
                if (!$urlRestriction->canView()) {
                    PostController::accessDeniedRedirect($urlRestriction);
                }
            }
        }
    }

    /**
     * Replace the post content if post has been restricted.
     * 
     * @param string $content
     * @return string
     */
    static function the_content($content) {
        if ($filter = UrlFilter::getInstanceByUrl($_SERVER['REQUEST_URI'])) {
            $urlRestriction = new UrlRestriction($filter);
            if (!$urlRestriction->canView()) {
                $content = self::loadFrontendView('access-denied');
            }
        }
        return $content;
    }

    static function admin_menu() {
        add_submenu_page(App::SLUG, App::getPluginName() . ' URL Filters', 'URL Filters', 'manage_options', self::getMenuSlug(), array(get_called_class(), 'render'));
    }

    static function getMenuSlug() {
        return App::SLUG . '-url-filters';
    }

    static function render() {
        wp_enqueue_style('cmacc-backend');
        wp_enqueue_style('cmacc-settings');
        wp_enqueue_script('cmacc-backend');
        echo self::loadView('backend/template', array(
            'title'   => App::getPluginName() . ' URL Filters',
            'nav'     => self::getBackendNav(),
            'content' => self::getContent(),
        ));
    }

    static function getContent() {
        return self::loadBackendView('index', array(
			'filters' => UrlFilter::getAllInstances(),
			'nonce'   => wp_create_nonce(self::ACTION_SAVE),
        ));
    }

    static function processRequest() {

        // Save filters
        $action = filter_input(INPUT_POST, self::PARAM_ACTION);
        $nonce = filter_input(INPUT_POST, 'nonce');
        if ($action == self::ACTION_SAVE AND $nonce AND wp_verify_nonce($nonce, self::ACTION_SAVE)) {
            self::saveUrlFilters();
            wp_safe_redirect($_SERVER['REQUEST_URI']);
            exit;
        }

        // Delete filter
        $action = filter_input(INPUT_GET, self::PARAM_ACTION);
        $nonce = filter_input(INPUT_GET, 'nonce');
        if ($action == self::ACTION_DELETE AND $nonce AND wp_verify_nonce($nonce, self::ACTION_DELETE)) {
            if ($id = filter_input(INPUT_GET, 'id')) {
                UrlFilter::deleteById($id);
                wp_safe_redirect(add_query_arg('page', self::getMenuSlug(), admin_url('admin.php')));
                exit;
            }
        }
    }

    static function saveUrlFilters() {

        if (!empty($_POST['filters']) AND is_array($_POST['filters'])) {

            $rawFilters = array();

            foreach ($_POST['filters'] as $fieldName => $values) {
                foreach ($values as $i => $value) {
                    if ($i > 0) {
                        $rawFilters[$i][$fieldName] = $value;
                    }
                }
            }

            foreach ($rawFilters as &$filter) {
                if (empty($filter['id'])) {
                    $filter['id'] = UrlFilter::create($filter['url']);
                }

                $restriction = new UrlRestriction(new UrlFilter($filter));
                $restriction->setRestriction(isset($filter['restriction']) ? $filter['restriction'] : UrlRestriction::RESTRICTION_NONE);
                $restriction->setAllowedRoles(isset($filter['roles']) ? $filter['roles'] : array());

                $restriction->setAllowedUsers(isset($filter['allowed_users']) ? $filter['allowed_users'] : array());
                $restriction->setNotAllowedUsers(isset($filter['not_allowed_users']) ? $filter['not_allowed_users'] : array());

                $restriction->setGuests(isset($filter['guests']) ? $filter['guests'] : UrlRestriction::RESTRICTION_NONE);

                $restriction->setRestrictedDays(isset($filter['days']) ? $filter['days'] : array());
                $restriction->setRestrictedDaysFromFirstAccess(isset($filter['days_from_first_access']) ? $filter['days_from_first_access'] : array());
                $restriction->setRestrictedFromDate(isset($filter['from_date']) ? $filter['from_date'] : array());
                $restriction->setRestrictedToDate(isset($filter['to_date']) ? $filter['to_date'] : array());
            }

            UrlFilter::saveAllRaw($rawFilters);
        }
    }

}