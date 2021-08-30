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
 * Save an answer
 *
 * @return void
 */
function copr_save_answer() {
	global $wpdb;
	header('Content-Type: application/json');
	$isAjax = (isset($_POST['is_ajax'])) ? boolval($_POST['is_ajax']) : false;
	if ((!isset($_POST)) || (!check_ajax_referer('submit_answers'))) {
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
			'question_number'	=>	$_POST['question_number'],
			'question'			=>	$_POST['question'],
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
		$sectionStore = new SectionStore($wpdb, $wpdb->prefix);
		$sectionId = $sectionStore->create($_POST['section_title'], $_POST['tag']);
		if ($sectionId !== false) {
			$payload['success'] = true;
		}
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

add_action( 'wp_ajax_copr_save_answer', 'copr_save_answer' );
add_action( 'divi_extensions_init', 'copr_initialize_extension' );
register_activation_hook( __FILE__, 'copr_activate_plugin' );
endif;
