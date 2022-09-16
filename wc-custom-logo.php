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
    'lib/class-wc-custom-logo-frontend.php',
		'lib/class-wc-custom-cart.php',
		'lib/class-wc-product-data.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
  }

	add_action( 'woocommerce_single_product_summary', function () {
		echo "<p style='font-size: 18px; font-weight: bold;'>Click <a style='text-decoration:underline;color:rgb(255, 136, 0);' href='https://beepro.us/upload-logo/'>here</a> if you haven't uploaded your design</p>";
	} );

	function wc_custom_logo_removebg(){
		if( isset( $_GET[ 'img' ] ) ){
			$util = \WC_CUSTOM_LOGO\IMAGE_UTIL::getInstance();
			print_r( $util->removebg( $_GET[ 'img' ] ) );
		}
		else{
			echo "No image passed.";
		}
		wp_die();
	}


	add_action( 'wp_ajax_wc_custom_logo_removebg', 'wc_custom_logo_removebg' );
	add_action( 'wp_ajax_nopriv_wc_custom_logo_removebg', 'wc_custom_logo_removebg' );


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


	function wc_get_label_designs(){

		$labels_titles = getWCLabelDesigns();
		$labels_cost = get_option( 'wc_settings_labels' );

		$data = array();

		foreach( $labels_titles as $label => $title ){
			$data[ $label ] = array(
				'label'	=> $title,
				'cost'	=> floatval( $labels_cost[ $label ] ) //floatval
			);
		}

		return $data;
		/*
		return array(
			'front' => array(
				'label' => 'Front',
				'cost'	=> 3.0
			),
			'back' 	=> array(
				'label' => 'Back',
				'cost'	=> 3.0
			),
			'chest' 	=> array(
				'label' => 'Left Chest',
				'cost'	=> 2.5
			),
		);
		*/
	}

	function wc_get_discounts_table(){
		$discount_values = get_option( 'wc_settings_discount' );
		return $discount_values;

		/*
		return array(
			'2400' 	=> 70,
			'1800'	=> 68,
			'1500'	=> 67,
			'1200'	=> 65,
			'900'		=> 62,
			'600'		=> 61,
			'500'		=> 60,
			'200'		=> 59,
			'100'		=> 55,
			'60'		=> 50,
			'48'		=> 45,
			'24'		=> 32,
			'12'		=> 20,
			'7'			=> 10
		);
		*/
	}

	function getWCDiscountBreaks(){
		return array(
			'2400', '1800', '1500', '1200', '900', '600',
			'500', '200', '100', '60', '48', '24', '12', '7'
		);
	}

	function getWCLabelDesigns(){
		return array(
			'front' 						=> 'Front',
			'back'							=> 'Back',
			'chest'							=> 'Left Chest',
			'embroidery-chest'	=> 'Embroidery Left Chest',
			'hat'								=> 'Hat Embroidery Front',
			'embroidery-right'	=> 'Embroidery Right Sleeve',
			'custom'						=> 'Enter Company Name & Phone Number / Last Name & Number'
		);
	}

	function getWCSizes(){
		return array(
			'YS', 'YM', 'YL', 'YXL', 'S', 'M', 'L', 'XL', '2XL', '3XL'
		);
	}

	function getWCSizesCosts(){
		return get_option( 'wc_settings_size' );
		/*
		return array(
			'2XL' => 2,
			'3XL'	=> 3
		);
		*/
	}







/*
add_filter( 'woocommerce_is_sold_individually', function(  $return, $product  ){
	return true;
}, 10, 2 );
*/


// https://codeinu.net/language/php/c1787170-get-additional-input-from-the-customer-in-woocommerce-product-page
// https://quadlayers.com/update-product-price-programmatically-in-woocommerce/





// Add order item meta.
add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta' , 10, 3 );
function add_order_item_meta ( $item_id, $cart_item, $cart_item_key ) {

	$keys = array( 'wc_custom_logo_image_src', 'wc_custom_label_design', 'wc_custom_sizes' );

	foreach( $keys as $key ){
		if ( isset( $cart_item[ $key ] ) ) {
			wc_add_order_item_meta( $item_id, $key, $cart_item[ $key ] );
		}
	}
}
