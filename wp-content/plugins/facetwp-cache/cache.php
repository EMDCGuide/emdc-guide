<?php

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FACETWP_CACHE', true );

// Process JSON
$json = file_get_contents( 'php://input' );
if ( 0 === strpos( $json, '{' ) ) {
    $post_data = json_decode( $json, true );
    if ( isset( $post_data['action'] ) && 'facetwp_refresh' == $post_data['action'] ) {
        $data = $post_data['data'];

        global $table_prefix;
        $wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
        $wpdb->prefix = $table_prefix;
    
        // Timestamp
        $now = date( 'Y-m-d H:i:s' );
    
        // MD5 hash
        $cache_name = md5( json_encode( $data ) );
    
        // Check for a cached version
        $sql = "
        SELECT value
        FROM {$wpdb->prefix}facetwp_cache
        WHERE name = '$cache_name' AND expire >= '$now'
        LIMIT 1";
        $value = $wpdb->get_var( $sql );
    
        // Return cached version and EXIT
        if ( null !== $value ) {
            header( 'Content-Type: application/json' );
            echo $value;
            exit;
        }
    }
}
