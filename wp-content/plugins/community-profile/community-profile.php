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
function copr_activate_plugin() {
	global $wpdb;
	$database = new Database($wpdb->get_charset_collate());
	$database->addStore(new SectionStore($wpdb, $wpdb->prefix));
	$database->addStore(new QuestionStore($wpdb, $wpdb->prefix));
	$database->addStore(new AnswerStore($wpdb, $wpdb->prefix));
	$database->install();
}

/**
 * Delete an answer
 *
 * @return void
 */
function copr_delete_answer() {
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
		if (!$canModerate) {
			if ($isAjax) {
				status_header(400, 'Invalid Request!');
				exit;
			} else {
				wp_redirect($_POST['_wp_http_referer']);
				exit;
			}
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
 * Save an answer
 *
 * @return void
 */
function copr_save_answer() {
	global $wpdb;
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
			'answer'			=>	$_POST['answer'],
			'question_choices'	=>	$_POST['question_choices'],
			'question_number'	=>	intval($_POST['question_number']),
			'question'			=>	$_POST['question'],
			'question_type'		=>	$_POST['question_type'],
			'section_title'		=>	$_POST['section_title'],
			'tag'				=>	$_POST['tag'],
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
		$repo = new AnswerRepository($wpdb, $wpdb->prefix);
		$payload['success'] = $repo->createOrUpdate(
			intval($_POST['group_id']),
			$userId,
			$_POST['section_title'],
			$_POST['tag'],
			$_POST['question_choices'],
			intval($_POST['question_number']),
			$_POST['question'],
			$_POST['question_type'],
			$_POST['answer']
		);
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
 * Add addition css and javascript files
 */
function copr_set_up_resource_files() {
	wp_register_style( 'copr_plugin', plugins_url('styles' . COPR_DS . 'community-profile.css', __FILE__) );
    wp_enqueue_style( 'copr_plugin' );
    wp_enqueue_script( 'copr_plugin', plugins_url('scripts' . COPR_DS . 'community-profile.js', __FILE__), array( 'jquery' ) );
	wp_enqueue_script( 'copr_plugin' );
}

/**
 * Initialization function for BuddyPress
 *
 * @return void
 */
function copr_bp_initialize() {
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
function copr_bp_tab_screen_content() {
	global $wpdb;
	$currentUserId = get_current_user_id();
	$groupId = bp_get_current_group_id();
	$canModerate = (groups_is_user_mod($currentUserId, $groupId) || groups_is_user_admin($currentUserId, $groupId));
	$repo =  new AnswerRepository($wpdb, $wpdb->prefix);
	$answers = $repo->findAllForGroup($groupId);
	require_once(COPR_ROOT_DIR . 'templates' . COPR_DS . 'profile-tab.php');
}

/**
 * Callback for BuddyPress to create the Community Profile tab
 *
 * @return void
 * @link https://wordpress.stackexchange.com/a/345664
 */
function copr_bp_tab() {
	add_action( 'bp_template_title', 'copr_bp_tab_screen_title' );
	add_action( 'bp_template_content', 'copr_bp_tab_screen_content' );

	$templates = array('groups/single/plugins.php', 'plugin-template.php');
	if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
	} else {
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
	}
}

add_action( 'wp_ajax_copr_save_answer', 'copr_save_answer' );
add_action( 'wp_ajax_copr_delete_answer', 'copr_delete_answer' );
add_action( 'divi_extensions_init', 'copr_initialize_extension' );
add_action( 'wp_enqueue_scripts', 'copr_set_up_resource_files' );
register_activation_hook( __FILE__, 'copr_activate_plugin' );
if (function_exists('bp_version')) {
	// BuddyPress hooks
	add_action( 'bp_init', 'copr_bp_initialize' );
}
endif;
