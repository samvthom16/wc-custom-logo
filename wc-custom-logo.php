<?php
	/*
    Plugin Name: WC Custom Logo
    Plugin URI: https://sputznik.com
    Description:
    Author: Samuel Thomas
    Version: 1.0.1
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
    'woocommerce.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
