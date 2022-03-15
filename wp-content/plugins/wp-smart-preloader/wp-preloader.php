<?php
/*
Plugin Name: WP Smart Preloader
Plugin URI: https://wordpress.org/plugins/wp-smart-preloader
Description: WP Smart Preloader is a Simple CSS spinners and throbbers made with CSS and minimal HTML markup.
Version: 1.14
Author: catchsquare
Author URI: http://catchsquare.com
License: GPLv2 or later
Text Domain: wp-smart-preloader
*/


/*Make sure we don't expose any info if called directly*/
if ( !function_exists( 'add_action' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

/*Define Constants for this plugin*/
define( 'SMART_PRELOADER_VERSION', '1.14' );
define( 'SMART_PRELOADER_PATH', plugin_dir_path( __FILE__ ) );
define( 'SMART_PRELOADER_URL', plugin_dir_url( __FILE__ ) );


// setting page
require SMART_PRELOADER_PATH."inc/wsp-option_page.php";
// uninstall hook
require SMART_PRELOADER_PATH."inc/wsp_uninstall.php";

