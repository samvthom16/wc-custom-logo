<?php
	/*
    Plugin Name: WC Custom Logo
    Plugin URI: https://sputznik.com
    Description: WooCommerce Add-on that allows customers to upload their logos on the products
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
		'lib/class-wc-base.php',
		'lib/class-image-util.php',
    'lib/class-wc-custom-logo-admin.php',
    'lib/class-wc-custom-logo-frontend.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
  }


	add_action( 'wp_ajax_wc_custom_logo_removebg', function(){
		if( isset( $_GET[ 'img' ] ) ){
			$util = \WC_CUSTOM_LOGO\IMAGE_UTIL::getInstance();
			print_r( $util->removebg( $_GET[ 'img' ] ) );
		}
		else{
			echo "No image passed.";
		}
		wp_die();
	} );





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

	function wc_inject_all_styles(){
		global $wc_enabled_ids;
		$ids_arr = array_unique( $wc_enabled_ids );
		$db_ids = array_unique( get_option( 'wc_logo_enabled_ids', array() ) );
		$total_ids = array_unique( array_merge( $db_ids, $ids_arr ) );

		/*
		echo "<div style='padding: 20px; border: red solid 1px;'>";
		print_r( $total_ids );
		echo "</div>";
		*/

		foreach( $total_ids as $id ){
			wc_inject_style( $id );
		}
	}

	function wc_default_logo_placeholder( $logo_type ){
		if( $logo_type == 'logo_white' ){
			return plugins_url( 'wc-custom-logo/assets/images/logo-placeholder-white.png' , dirname(__FILE__) );
		}
		return plugins_url( 'wc-custom-logo/assets/images/logo-placeholder-black.png' , dirname(__FILE__) );
	}


	$GLOBALS['wc_enabled_ids'] = array();

	add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ){
		global $wc_enabled_ids;
		$attr['data-behaviour'] = 'wc-custom-logo-product';
		$attr['data-post'] =  $attachment->ID;
		array_push( $wc_enabled_ids, $attachment->ID );

		if ( is_page( 'cart' ) || is_cart() ) {
			$attr['data-cart'] = 1;
		}

		return $attr;
	}, 5, 10 );

	// INJECT STYLES FOR THE FRONTEND
	add_action( 'wp_footer', function(){ wc_inject_all_styles(); } );





	// Displaying the checkboxes
	add_action( 'woocommerce_before_add_to_cart_button', 'add_fields_before_add_to_cart' );
	function add_fields_before_add_to_cart( ) {
		global $product;
	?>
		<div class="wc-custom-logo-extras">
			<input type='hidden' name='wc_custom_logo_image_src' value='<?php echo WC()->session->get( 'wc_custom_logo' );?>' />
		</div>
		<?php
	}

// Add data to cart item
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data', 25, 2 );
function add_cart_item_data( $cart_item_data, $product_id ) {
	$key = 'wc_custom_logo_image_src';
	if( isset( $_POST[$key] ) ) $cart_item_data[$key] = $_POST[$key];
	return $cart_item_data;
}


// Display custom data on cart and checkout page.
add_filter( 'woocommerce_get_item_data', 'get_item_data' , 25, 2 );
function get_item_data ( $cart_data, $cart_item ) {
	$key = 'wc_custom_logo_image_src';

	$image_id = ( isset( $cart_item['data'] ) && isset( $cart_item['data']->get_data()['image_id'] ) ) ? $cart_item['data']->get_data()['image_id'] : 0;

	if( !empty( $cart_item[ $key ] ) && $image_id ){
		?>
		<!--style>
			.woocommerce-cart .product-thumbnail .wc-custom-logo-product-parent-<?php _e( $image_id )?>::after{
				background-image: url('<?php _e( $cart_item[ $key ] );?>') !important;
			}
		</style-->
		<p style='margin-top: 20px; text-decoration: underline; font-size:small;'><a data-rel='prettyPhoto' href='<?php echo $cart_item[ $key ];?>'>With Custom Logo</a><p>

		<?php

	}
	return $cart_data;
}


// Add order item meta.
add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta' , 10, 3 );
function add_order_item_meta ( $item_id, $cart_item, $cart_item_key ) {
	$key = 'wc_custom_logo_image_src';
	if ( isset( $cart_item[ $key ] ) ) {
		wc_add_order_item_meta( $item_id, $key, $cart_item[ $key ] );
	}
}
