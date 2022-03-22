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
 * Enqueue styles & scripts
 */
function child_enqueue_styles() {
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );
	wp_enqueue_script( 'astra-child-theme-js', get_stylesheet_directory_uri() . '/child-application.js', array('jquery'), CHILD_THEME_ASTRA_CHILD_VERSION, true );
}

/**
 * Our intialization code
 */
function child_init() {
	register_post_type('guide_resource', array(
		'exclude_from_search'	=>	false,
		'has_archive'			=>	true,
		'label'					=>	__('Resources'),
		'singular_label'		=>	__('Resource'),
		'description'			=>	__('Scripture engagement resources to support your ministry.'),
		'public' 				=>	true,
		'show_ui'				=>	true,
		'capability_type'		=>	'post',
		'hierarchical'			=>	false,
		'menu_icon'				=>	'dashicons-admin-media',
		'menu_position'			=>	20,
		'rewrite' 				=> 	array('slug' => 'resources'),
		'query_var'				=>	false,
		'taxonomies'			=>	array('post_tag','category'),
		'supports'				=>	array('title', 'editor', 'excerpt', 'thumbnail'),
        'show_in_rest' 			=> 	true
	));
}
/**
 * @link https://wpdevelopment.courses/articles/custom-spacing-settings/
 */
function child_enable_gutenberg_custom_spacing() {
	add_theme_support( 'custom-spacing' );
}
/**
 * Change the archive title
 *
 * @link https://wordpress.stackexchange.com/a/175903
 */
function child_get_archive_title($title) {
	if (is_post_type_archive('guide_resource')) {
		$title = 'Resources';
	}
	return $title;
}
/**
 * Fixes the sort order on Archive page
 * Adds the resources to search
 * @link https://wordpress.stackexchange.com/a/39818
 * @link https://www.dhirenpatel.me/cpt-wordpress-search-result/
 */
function child_pre_get_posts($query) {
	if((is_post_type_archive('guide_resource')) && (!isset($_GET['_search']))) {
		$query->set( 'order', 'ASC' );
		$query->set( 'orderby', 'title' );
	}

	return $query;
};
/**
 * Fix the ordering of our custom post type (Navigation)
 *
 * @link https://wordpress.stackexchange.com/a/184797
 */
function child_filter_next_post_sort($sort) {
    global $post;
    if (get_post_type($post) == 'guide_resource') {
        $sort = "ORDER BY p.post_title ASC LIMIT 1";
    }
    else{
        $sort = "ORDER BY p.post_date ASC LIMIT 1";
    }
    return $sort;
}
function child_filter_next_post_where($where) {
    global $post, $wpdb;
    if (get_post_type($post) == 'guide_resource') {
        return $wpdb->prepare("WHERE p.post_title > '%s' AND p.post_type = '". get_post_type($post)."' AND p.post_status = 'publish'",$post->post_title);
    }
    else{
        return $wpdb->prepare( "WHERE p.post_date > '%s' AND p.post_type = '". get_post_type($post)."' AND p.post_status = 'publish'", $post->post_date);
    }
}

function child_filter_previous_post_sort($sort) {
    global $post;
    if (get_post_type($post) == 'guide_resource') {
        $sort = "ORDER BY p.post_title DESC LIMIT 1";
    }
    else{
        $sort = "ORDER BY p.post_date DESC LIMIT 1";
    }
    return $sort;
}
function child_filter_previous_post_where($where) {
    global $post, $wpdb;
    if (get_post_type($post) == 'guide_resource') {
        return $wpdb->prepare("WHERE p.post_title < '%s' AND p.post_type = '". get_post_type($post)."' AND p.post_status = 'publish'",$post->post_title);
    }
    else{
        return $wpdb->prepare( "WHERE p.post_date < '%s' AND p.post_type = '". get_post_type($post)."' AND p.post_status = 'publish'", $post->post_date);
    }
}
/**
 * ASTRA Theme Fix: Allow related posts on a custom post type
 * This was a reply from their tech support.
 */
function child_related_posts_supported_post_types($type) {
	global $post;

	if ( get_post_type($post) === 'guide_resource' ) {
		$type = 'guide_resource';
	}
	return $type;
}
/**
 * Hide the search form on the resources page since it is in the sidebar.
 */
function child_get_search_form($form) {
	if ( is_post_type_archive('guide_resource') ) {
		return '';
	}
	return $form;
}
/**
 * Add special navigation to the footer of each resource
 */
function child_the_content($text) {
	global $post;
	if( get_post_type($post) === 'guide_resource' ) {
		$customField = 'wpf26052_4=' . urlencode('Resource: ' . $post->post_title);
		$buttons = '<div class="resource-nav-buttons">' .
			'<button onclick="location.href=\'/resources/\';">' . __('Back to Resources') . '</button>' .
			'<button class="contact-provider" onclick="location.href=\'/contact-resource-provider/?' . $customField . '\';">' . __('Contact Resource Provider') . '</button>' .
		'</div>';
		$text = $text . $buttons;
	}
	return $text;
}
/**
 * Set the breakpoint for tablets
 * @link https://wpastra.com/docs/set-update-breakpoints-tablet-mobile-in-astra/
 */
function child_astra_tablet_breakpoint() {
	return 983;
}

add_action( 'after_setup_theme', 'child_enable_gutenberg_custom_spacing' );
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );
add_action( 'init', 'child_init' );
add_action( 'pre_get_posts', 'child_pre_get_posts' );
add_filter( 'get_the_archive_title', 'child_get_archive_title' );
add_filter( 'get_next_post_sort',   'child_filter_next_post_sort' );
add_filter( 'get_next_post_where',  'child_filter_next_post_where' );
add_filter( 'get_previous_post_sort',  'child_filter_previous_post_sort' );
add_filter( 'get_previous_post_where', 'child_filter_previous_post_where' );
add_filter( 'get_search_form', 'child_get_search_form' );
add_filter( 'astra_related_posts_supported_post_types', 'child_related_posts_supported_post_types' );
add_filter( 'astra_tablet_breakpoint', 'child_astra_tablet_breakpoint');
add_filter( 'the_content', 'child_the_content', 1);
