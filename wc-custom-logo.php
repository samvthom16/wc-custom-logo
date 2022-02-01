<?php
	/*
    Plugin Name: WC Custom Logo
    Plugin URI: https://sputznik.com
    Description: WooCommerce Add-on that allows customers to upload custom logo on the products
    Author: Samuel Thomas
    Version: 1.0.0
    Author URI: https://sputznik.com
    */


	define( 'WC_CUSTOM_LOGO_VERSION', time() );

  /**
  * Check if WooCommerce is active
  **/
  if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return;
  }



	$inc_files = array(
    'lib/class-wc-custom-logo-admin.php',
    'lib/class-wc-custom-logo-frontend.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
  }

	function wc_inject( $post_id, $type = 'script' ){
		$dimensions = get_post_meta( $post_id, 'wc_logo_dimensions', true );
		if( $dimensions ){
			include( "lib/templates/inject_$type.php" );
		}
	}

  function wc_inject_script( $post_id ){
		wc_inject( $post_id, 'script' );
	}

	function wc_inject_style( $post_id ){
		wc_inject( $post_id, 'style' );
	}

	



	$GLOBALS['wc_enabled_ids'] = array();

	add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ){
		global $wc_enabled_ids;
		$attr['data-behaviour'] = 'wc-custom-logo-product';
		$attr['data-post'] =  $attachment->ID;
		array_push( $wc_enabled_ids, $attachment->ID );
		//wc_inject_script( $attachment->ID );
		return $attr;
	}, 5, 10 );


	add_action( 'wp_footer', function(){
		global $wc_enabled_ids;
		$ids_arr = array_unique( $wc_enabled_ids );
		$db_ids = array_unique( get_option( 'wc_logo_enabled_ids', array() ) );
		$total_ids = array_unique( array_merge( $db_ids, $ids_arr ) );
		foreach( $total_ids as $id ){
			wc_inject_style( $id );
		}
	} );
