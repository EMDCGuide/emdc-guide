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


if ( ! function_exists( 'copr_initialize_extension' ) ):
/**
 * Creates the extension's main class instance.
 *
 * @since 1.0.0
 */
function copr_initialize_extension() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/CommunityProfile.php';
}

/**
 * Save an answer
 *
 * @return void
 */
function copr_save_answer() {
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
		$payload['success'] = true;
		/**
		 * Save the data
		 */
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
endif;
