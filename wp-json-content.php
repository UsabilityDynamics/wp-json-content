<?php
/**
 * Plugin Name: WP JSON Content
 * Plugin URI: https://udx.io
 * Description: Converts posts to JSON and allows to hook into it to customize the output.
 * Author: UDX
 * Version: 1.0.0
 * Requires at least: 4.0
 * Tested up to: 4.9.2
 */

if ( !defined( 'WPJC_GET_ARG_NAME' ) ) {
  define( 'WPJC_GET_ARG_NAME', 'json' );
}

/**
 * Possible `get` or `query`
 */
if ( !defined( 'WPJC_JSONIFY_METHOD' ) ) {
  define( 'WPJC_JSONIFY_METHOD', 'query' );
}

if( !function_exists( 'wpjc' ) ) {
  function wpjc() {
    if ( class_exists('\UsabilityDynamics\WPJC\Bootstrap') ) return \UsabilityDynamics\WPJC\Bootstrap::get_instance();
    return false;
  }
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
  require_once ( dirname( __FILE__ ) . '/vendor/autoload.php' );
  wpjc();
} else {
  add_action( 'admin_notices', function() {
    echo '<div class="error fade" style="padding:11px;">WP JSON Content plugin is broken. Please re-install it.</div>';
  });
}