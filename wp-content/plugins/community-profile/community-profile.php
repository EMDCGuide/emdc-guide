<?php
/*
Plugin Name: Community Profile
Plugin URI:
Description: A plugin for tracking the SE journey of all members within a BuddyPress community. This plugin depends on BuddyPress, WooCommerce and a Divi Theme.
Version:     1.0.0
Author:      Missional Digerati
Author URI:  https://missionaldigerati.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: copr-community-profile
Domain Path: /languages

Community Profile is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Community Profile is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Community Profile. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/
require_once(plugin_dir_path( __FILE__ ) . DIRECTORY_SEPARATOR . 'autoloader.php');

use MissionalDigerati\CommunityProfile\Database;
use MissionalDigerati\CommunityProfile\Stores\AnswerStore;
use MissionalDigerati\CommunityProfile\Stores\QuestionStore;
use MissionalDigerati\CommunityProfile\Stores\SectionStore;
use MissionalDigerati\CommunityProfile\Repositories\AnswerRepository;

if ( ! function_exists( 'copr_initialize_extension' ) ):

define('COPR_ROOT_DIR', plugin_dir_path( __FILE__ ));
define('COPR_DS', DIRECTORY_SEPARATOR);
define('COPR_GROUP_ID_COOKIE', 'copr-group-selected');

/**
 * Creates the extension's main class instance.
 *
 * @since 1.0.0
 */
function copr_initialize_extension() {
	require_once(COPR_ROOT_DIR . 'includes/CommunityProfile.php');
}

/**
 * Run code when the plugin is activated
 *
 * @return void
 */
function copr_activate_plugin()
{
	global $wpdb;
	$database = new Database($wpdb->get_charset_collate());
	$database->addStore(new SectionStore($wpdb, $wpdb->prefix));
	$database->addStore(new QuestionStore($wpdb, $wpdb->prefix));
	$database->addStore(new AnswerStore($wpdb, $wpdb->prefix));
	$database->install();
}

/**
 * POST: Add a new group
 */
function copr_add_group()
{
	$userId = get_current_user_id();
	$isAjax = (isset($_POST['is_ajax'])) ? boolval($_POST['is_ajax']) : false;
	if ((!isset($_POST)) || (!check_ajax_referer('add_new_group')) || ($userId === 0)) {
		if ($isAjax) {
			status_header(400, 'Invalid Request!');
			exit;
		} else {
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
	$parts = explode('?', $_POST['_wp_http_referer']);
	$returnUrl = $parts[0];
	if (!function_exists('bp_version')) {
		if ($isAjax) {
			status_header(500, 'BuddyPress not Installed!');
			exit;
		} else {
			wp_redirect($returnUrl);
			exit;
		}
	}
	$payload = array(
		'data'		=>	array(
			'id'					=>	-1,
			'group_name'			=>	$_POST['group_name'],
			'group_description'		=>	$_POST['group_desc'],
			'group_type'			=>	$_POST['group_type'],
		),
		'errors'	=>	array(),
		'success'	=>	false,
	);
	if (!$_POST['group_name']) {
		$payload['errors'][] = array(
			'field'	=>	'group_name',
			'error'	=>	__('The group name cannot be blank!', 'copr-my-extension'),
		);
	}
	if (!$_POST['group_desc']) {
		$payload['errors'][] = array(
			'field'	=>	'group_desc',
			'error'	=>	__('The group description cannot be blank!', 'copr-my-extension'),
		);
	}
	if (!$_POST['group_type']) {
		$payload['errors'][] = array(
			'field'	=>	'group_type',
			'error'	=>	__('The group type cannot be blank!', 'copr-my-extension'),
		);
	}
	if (count($payload['errors']) > 0) {
		if ($isAjax) {
			header('Content-Type: application/json');
			echo json_encode($payload);
			exit;
		} else {
			$errorFields = [];
			foreach ($payload['errors'] as $error) {
				$errorFields[] = $error['field'];
			}
			$url = $returnUrl . '?error_fields=' . join(',', $errorFields);
			$url .= '&group_name=' . urlencode($_POST['group_name']);
			$url .= '&group_desc=' . urlencode($_POST['group_desc']);
			$url .= '&group_type=' . urlencode($_POST['group_type']);
			wp_redirect($url);
			exit;
		}
	}
	$settings = array(
		'group_id'     => 0,
		'creator_id'   => $userId,
		'name'         => $_POST['group_name'],
		'description'  => $_POST['group_desc'],
		'slug'         => '',
		'status'       => $_POST['group_type']
	);
	$id = groups_create_group($settings);
	if ($id) {
		$payload['success'] = true;
		$payload['data']['id'] = intval($id);
		setcookie(COPR_GROUP_ID_COOKIE, intval($id), 0, '/', '', $secure);
	}
	if ($isAjax) {
		header('Content-Type: application/json');
		echo json_encode($payload);
		exit;
	} else {
		wp_redirect($returnUrl);
		exit;
	}
}

/**
 * POST: Delete an answer
 *
 * @return void
 */
function copr_delete_answer()
{
	global $wpdb;
	$userId = get_current_user_id();
	$groupId = $_POST['group_id'];
	$answerId = $_POST['answer_id'];
	$store = new AnswerStore($wpdb, $wpdb->prefix);
	$answer = $store->findById($answerId);
	$isAjax = (isset($_POST['is_ajax'])) ? boolval($_POST['is_ajax']) : false;
	if ((!isset($_POST)) || (!check_ajax_referer('delete_answer')) || ($userId === 0)) {
		if ($isAjax) {
			status_header(400, 'Invalid Request!');
			exit;
		} else {
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
	if (!$answer) {
		if ($isAjax) {
			status_header(400, 'Invalid Request!');
			exit;
		} else {
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
	if (function_exists('bp_version')) {
		// BuddyPress is available
		$canModerate = (groups_is_user_mod($userId, $groupId) || groups_is_user_admin($userId, $groupId));
		if ((!$canModerate) && ($answer->user_id !== $userId)) {
			if ($isAjax) {
				status_header(401, 'Unauthorized!');
				exit;
			} else {
				wp_redirect($_POST['_wp_http_referer']);
				exit;
			}
		}
	}
	$payload = array(
		'success'	=>	false,
	);
	$payload['success'] = ($store->delete($answerId) !== false);
	if ($isAjax) {
		header('Content-Type: application/json');
		echo json_encode($payload);
		exit;
	} else {
		wp_redirect($_POST['_wp_http_referer']);
		exit;
	}
}

/**
 * GET: Retrieve an array keyed with the question unique hash and it's answer
 * Each tag will have its own array. Example:
 *
 * [
 * 		"c1-a": [
 * 			"344122WWQEE223": "I love Ice Cream!"
 * 		]
 * ]
 *
 *  Tags should be comma seperated
 *
 * @return void
 */
function copr_get_answers()
{
	global $wpdb;
	$repo = new AnswerRepository($wpdb, $wpdb->prefix);
	$userId = get_current_user_id();
	$groupId = -1;
	if (isset($_COOKIE) && isset($_COOKIE[COPR_GROUP_ID_COOKIE])) {
		$groupId = intval($_COOKIE[COPR_GROUP_ID_COOKIE]);
	}
	if ($groupId === -1) {
		status_header(400, 'Incorrect group id!');
		exit;
	}
	if (!isset($_GET['tags'])) {
		status_header(400, 'Invalid tags!');
		exit;
	}
	$tags = explode(',', $_GET['tags']);
	if (count($tags) === 0) {
		status_header(400, 'Missing tags!');
		exit;
	}
	$answers = array();
	foreach ($tags as $tag) {
		$answers[$tag] = array();
		$results = $repo->findAllBySectionTag($tag, $groupId, $userId);
		foreach ($results as $result) {
			$answers[$tag][$result->unique_hash] = $result->answer;
		}
	}
	header('Content-Type: application/json');
	echo json_encode($answers);
	exit;
}

/**
 * GET: Get a HTML template.
 *
 * @return void
 */
function copr_get_template()
{
	global $wpdb;
	$repo = new AnswerRepository($wpdb, $wpdb->prefix);
	$currentUserId = get_current_user_id();
	if (!isset($_GET['group_id'])) {
		status_header(400, 'Invalid Request!');
		exit;
	}
	if (!isset($_GET['answer_id'])) {
		status_header(400, 'Invalid Request!');
		exit;
	}
	if (!isset($_GET['template_name'])) {
		status_header(400, 'Invalid Request!');
		exit;
	}
	if (!function_exists('bp_version')) {
		status_header(500, 'BuddyPress not Installed!');
		exit;
	}
	$groupId = intval($_GET['group_id']);
	$templateName = $_GET['template_name'];
	$canModerate = (groups_is_user_mod($currentUserId, $groupId) || groups_is_user_admin($currentUserId, $groupId));
	$id = intval($_GET['answer_id']);
	$answer = $repo->findById($id);
	$templateFile = COPR_ROOT_DIR . 'templates' . COPR_DS . $templateName . '.php';
	if (file_exists($templateFile)) {
		require_once($templateFile);
	}
}

/**
 * POST: Save an answer
 *
 * @return void
 */
function copr_save_answer()
{
	global $wpdb;
	$repo = new AnswerRepository($wpdb, $wpdb->prefix);
	$userId = get_current_user_id();
	$isAjax = (isset($_POST['is_ajax'])) ? boolval($_POST['is_ajax']) : false;
	if ((!isset($_POST)) || (!check_ajax_referer('submit_answers')) || ($userId === 0)) {
		if ($isAjax) {
			status_header(400, 'Invalid Request!');
			exit;
		} else {
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
	$payload = array(
		'data'		=>	array(
			'id'				=>	-1,
			'answer'			=>	$_POST['answer'],
			'question_choices'	=>	$_POST['question_choices'],
			'question_number'	=>	intval($_POST['question_number']),
			'question'			=>	$_POST['question'],
			'question_type'		=>	$_POST['question_type'],
			'section_title'		=>	$_POST['section_title'],
			'section_url'		=>	$_POST['section_url'],
			'section_tag'		=>	$_POST['section_tag'],
		),
		'errors'	=>	array(),
		'success'	=>	false,
	);
	if (!$_POST['answer']) {
		$payload['success'] = false;
		$payload['errors'][] = array(
			'field'	=>	'answer',
			'error'	=>	esc_html__('The answer cannot be blank!', 'copr-my-extension'),
		);
	} else {
		/**
		 * Save the data
		 */
		$payload['success'] = $repo->createOrUpdate(
			intval($_POST['group_id']),
			$userId,
			$_POST['section_title'],
			$_POST['section_tag'],
			$_POST['section_url'],
			$_POST['question_choices'],
			intval($_POST['question_number']),
			$_POST['question'],
			$_POST['question_type'],
			$_POST['answer']
		);
		$payload['data']['id'] = $repo->lastId;
	}
	if ($isAjax) {
		header('Content-Type: application/json');
		echo json_encode($payload);
		exit;
	} else {
		wp_redirect($_POST['_wp_http_referer']);
		exit;
	}
}

/**
 * Select the specific group.
 *
 * @return void
 */
function copr_select_group()
{
	$userId = get_current_user_id();
	$isAjax = (isset($_POST['is_ajax'])) ? boolval($_POST['is_ajax']) : false;
	$secure = ((isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] === 'on'));
	if ((!isset($_POST)) || (!check_ajax_referer('select_group')) || ($userId === 0)) {
		if ($isAjax) {
			status_header(400, 'Invalid Request!');
			exit;
		} else {
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
	if ((!isset($_POST['group_id'])) || (intval($_POST['group_id']) === -1)) {
		if ($isAjax) {
			status_header(400, 'Invalid Request!');
			exit;
		} else {
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
	if (!function_exists('bp_version')) {
		if ($isAjax) {
			status_header(500, 'BuddyPress not Installed!');
			exit;
		} else {
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
	$isMember = (groups_is_user_member($userId, $_POST['group_id']));
	if (!$isMember) {
		if ($isAjax) {
			status_header(401, 'Unauthorized!');
			exit;
		} else {
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
	setcookie(COPR_GROUP_ID_COOKIE, intval($_POST['group_id']), 0, '/', '', $secure);
	$payload = array(
		'success'	=>	true,
	);
	if ($isAjax) {
		header('Content-Type: application/json');
		echo json_encode($payload);
		exit;
	} else {
		wp_redirect($_POST['_wp_http_referer']);
		exit;
	}
}
/**
 * Update an answer using it's id
 *
 * @return void
 */
function copr_update_answer_by_id()
{
	global $wpdb;
	$userId = get_current_user_id();
	$groupId = $_POST['group_id'];
	$answerId = $_POST['answer_id'];
	$store = new AnswerStore($wpdb, $wpdb->prefix);
	$answer = $store->findById($answerId);
	$isAjax = (isset($_POST['is_ajax'])) ? boolval($_POST['is_ajax']) : false;
	if ((!isset($_POST)) || (!check_ajax_referer('edit_answer')) || ($userId === 0)) {
		if ($isAjax) {
			status_header(400, 'Invalid Request!');
			exit;
		} else {
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
	if (!$answer) {
		if ($isAjax) {
			status_header(400, 'Invalid Request!');
			exit;
		} else {
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
	if (function_exists('bp_version')) {
		// BuddyPress is available
		$canModerate = (groups_is_user_mod($userId, $groupId) || groups_is_user_admin($userId, $groupId));
		if ((!$canModerate) && ($answer->user_id !== $userId)) {
			if ($isAjax) {
				status_header(401, 'Unauthorized!');
				exit;
			} else {
				wp_redirect($_POST['_wp_http_referer']);
				exit;
			}
		}
	}
	$payload = array(
		'success'	=>	false,
	);
	$payload['success'] = ($store->updateById($answerId, $_POST['answer']) !== false);
	if ($isAjax) {
		header('Content-Type: application/json');
		echo json_encode($payload);
		exit;
	} else {
		wp_redirect($_POST['_wp_http_referer']);
		exit;
	}
}

/**
 * Initialization function for BuddyPress
 *
 * @return void
 */
function copr_bp_initialize()
{
	global $bp;
	$user_access = false;
    $group_link = '';
    if( bp_is_active('groups') && !empty($bp->groups->current_group) ){
        $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
        $user_access = $bp->groups->current_group->user_has_access;
        bp_core_new_subnav_item( array(
            'name' 				=> __( 'Community Profile', 'copr-my-extension' ),
            'slug' 				=> 'community-profile',
            'parent_url' 		=> $group_link,
            'parent_slug' 		=> $bp->groups->current_group->slug,
            'screen_function' 	=> 'copr_bp_tab',
            'position' 			=> 50,
            'user_has_access' 	=> $user_access,
            'item_css_id' 		=> 'custom'
        ));
    }
}

/**
 * Get the title for our BuddyPress tab
 *
 * @return string 	The title
 */
function copr_bp_tab_screen_title() {
	return __( 'Community Profile', 'copr-my-extension' );
}

/**
 * Get the content for our BuddyPress tab
 *
 * @return string 	The content
 */
function copr_bp_tab_screen_content()
{
	global $wpdb;
	$currentUserId = get_current_user_id();
	$groupId = bp_get_current_group_id();
	$canModerate = (groups_is_user_mod($currentUserId, $groupId) || groups_is_user_admin($currentUserId, $groupId));
	$repo =  new AnswerRepository($wpdb, $wpdb->prefix);
	$answers = $repo->findAllForGroup($groupId);
	require_once(COPR_ROOT_DIR . 'templates' . COPR_DS . 'profile_tab.php');
}

/**
 * Callback for BuddyPress to create the Community Profile tab
 *
 * @return void
 * @link https://wordpress.stackexchange.com/a/345664
 */
function copr_bp_tab()
{
	add_action( 'bp_template_title', 'copr_bp_tab_screen_title' );
	add_action( 'bp_template_content', 'copr_bp_tab_screen_content' );

	$templates = array('groups/single/plugins.php', 'plugin-template.php');
	if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
	} else {
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
	}
}

/**
 * Prepare for the new user.
 *
 * @param  string $userLogin The username
 * @param  object $user      The user
 * @return void
 */
function copr_user_login($userLogin, $user) {
	// Remove our cookie to protect from cookies with a different account
	// Wanted to do this on logout but the method never called.
	if (isset($_COOKIE[COPR_GROUP_ID_COOKIE])) {
	    unset($_COOKIE[COPR_GROUP_ID_COOKIE]);
	    setcookie(COPR_GROUP_ID_COOKIE, null, -1, '/');
	}
}

add_action('wp_login', 'copr_user_login', 10, 2);
add_action( 'wp_ajax_copr_get_answers', 'copr_get_answers' );
add_action( 'wp_ajax_copr_add_group', 'copr_add_group' );
add_action( 'wp_ajax_copr_select_group', 'copr_select_group' );
add_action( 'wp_ajax_copr_get_template', 'copr_get_template' );
add_action( 'wp_ajax_copr_save_answer', 'copr_save_answer' );
add_action( 'wp_ajax_copr_delete_answer', 'copr_delete_answer' );
add_action( 'wp_ajax_copr_update_answer_by_id', 'copr_update_answer_by_id' );
add_action( 'divi_extensions_init', 'copr_initialize_extension' );
register_activation_hook( __FILE__, 'copr_activate_plugin' );
if (function_exists('bp_version')) {
	// BuddyPress hooks
	add_action( 'bp_init', 'copr_bp_initialize' );
}
endif;
