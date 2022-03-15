<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

/**
 * Our intialization code
 */
function child_init() {
	register_post_type('guide_resource', array(
		'label'				=>	__('Resources'),
		'singular_label'	=>	__('Resource'),
		'description'		=>	__('Scripture engagement resources to support your ministry.'),
		'public' 			=>	true,
		'show_ui'			=>	true,
		'capability_type'	=>	'post',
		'hierarchical'		=>	false,
		'menu_icon'			=>	'dashicons-admin-media',
		'menu_position'		=>	20,
		'rewrite'			=>	false,
		'query_var'			=>	false,
		'taxonomies'		=>	array('post_tag','category'),
		'supports'			=>	array('title')
	));
}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );
add_action( 'init', 'child_init' );
