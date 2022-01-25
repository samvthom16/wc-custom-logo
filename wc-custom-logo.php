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

  function wc_get_dimensions( $post_id ){
    $dimensions = array(
      'width' => 0,
      'top'   => 0,
      'left'  => 0
    );

    foreach( $dimensions as $key => $val ){
      $option = get_post_meta( $post_id, 'wc_logo_' . $key, true );
      if( isset( $option ) ){
        $dimensions[ $key ] = $option;
      }
    }
    return $dimensions;
  }

  /*
  add_filter( 'woocommerce_single_product_image_thumbnail_html', 'add_class_to_thumbs', 10, 2 );
  function add_class_to_thumbs( $html, $attachment_id ) {
    if ( get_post_thumbnail_id() !== intval( $attachment_id ) ) {
      global $post;

      $html = str_replace( '<a ', '<a data-behaviour="wc-custom-logo-product" data-post="' . $post->ID . '" ', $html );

      $dimensions = wc_get_dimensions( $post->ID );

      ob_start();
      ?>

      <script type="text/javascript">
				if( window.browserData === undefined || window.browserData[ 'wc_logo' ] === undefined || window.browserData[ 'wc_logo' ][ <?php _e( $post->ID )?> ] === undefined ){
					var data = window.browserData = window.browserData || {};
					window.browserData[ 'wc_logo' ] = window.browserData[ 'wc_logo' ] || {};
					window.browserData[ 'wc_logo' ][ <?php _e( $post->ID )?> ] = <?php echo wp_json_encode( $dimensions );?>;
				}
			</script>

      <?php
      $html .= ob_get_clean();
    }
	  return $html;
  }

  function remove_image_zoom_support() {
    remove_theme_support( 'wc-product-gallery-zoom' );
  }
  add_action( 'wp', 'remove_image_zoom_support', 100 );
  */
