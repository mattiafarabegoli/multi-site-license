<?php
/*
Plugin Name: Updates Plugin
Plugin URI: https://www.example.com/updates-plugin
Description: Plugin to check all updates.
Version: 1.0.1
Author: Kobold Studio
Author URI: https://www.example.com/
License: GPL2
*/

defined( 'ABSPATH' ) || exit;
! defined( 'UPDATESPLUGIN_DIR' ) && define( 'UPDATESPLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once UPDATESPLUGIN_DIR . '/class-updates-plugin.php';

/**
 * Database table creation
 * 
 */
function up_create_database() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'up_license';

    $sql = "CREATE TABLE $table_name (
      id INT NOT NULL AUTO_INCREMENT,
      license_key VARCHAR(255) NOT NULL,
      license_owner VARCHAR(255) NOT NULL,
      PRIMARY KEY (id)
    );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta( $sql );
}

/**
 * Register installation function
 * 
 */
register_activation_hook(__FILE__, 'up_create_database');

/**
 * Load scripts and styles and pass AJAX url to scripts
 * 
 */
function myplugin_enqueue_scripts() {
    wp_enqueue_script( 'up-script', plugins_url( '/js/class-button-handler.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
	wp_enqueue_style( 'myplugin-style', plugins_url( '/css/up-style.css', __FILE__ ) );
	wp_localize_script( 'up-script', 'up_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php')) );
}
add_action( 'admin_enqueue_scripts', 'myplugin_enqueue_scripts' );


?>