<?php
	/*
    Plugin Name: WC Custom Logo
    Plugin URI: https://sputznik.com
    Description: WooCommerce Add-on that allows customers to upload custom logo on the products
    Author: Samuel Thomas
    Version: 1.0.0
    Author URI: https://sputznik.com
    */


	//define( 'INPURSUIT_VERSION', time() );

  /**
  * Check if WooCommerce is active
  **/
  if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return;
  }



	$inc_files = array(
    'class-wc-custom-logo-admin.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
