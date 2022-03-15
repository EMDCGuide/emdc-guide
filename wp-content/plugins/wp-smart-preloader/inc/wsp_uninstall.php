<?php
/**
 * uninstall hook
 */
 register_uninstall_hook( __FILE__, array( 'WP_smart_preloader', 'wsp_uninstall' ) );