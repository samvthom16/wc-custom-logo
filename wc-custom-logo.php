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

  function wc_inject_script( $post_id ){
		$dimensions = get_post_meta( $post_id, 'wc_logo_dimensions', true );
		if( $dimensions ){
			include( 'lib/templates/inject_script.php' );
		}
	}

	//add_filter( 'woocommerce_single_product_image_html', 'add_class_to_thumbs', 10, 2 );
  add_filter( 'woocommerce_single_product_image_thumbnail_html', 'add_class_to_thumbs', 10, 2 );
  function add_class_to_thumbs( $html, $attachment_id ) {
		$html = str_replace( '<a ', '<a data-behaviour="wc-custom-logo-product" data-post="' . $attachment_id . '" ', $html );
		ob_start();
		wc_inject_script( $attachment_id );
		$html .= ob_get_clean();
		return $html;
  }

  function remove_image_zoom_support() {
  	//remove_theme_support( 'wc-product-gallery-zoom' );
  }
  add_action( 'wp', 'remove_image_zoom_support', 100 );
