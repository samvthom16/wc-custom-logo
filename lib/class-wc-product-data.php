<?php

namespace WC_CUSTOM_LOGO;

use WC_CUSTOM_LOGO_ADMIN;

class WC_PRODUCT_DATA extends WC_BASE{

  function __construct(){
    add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'addFieldsBeforeCart' ) );
  }

  function getSettings( $product_id ){
    $admin = new WC_CUSTOM_LOGO_ADMIN;
    return $admin->getSettings( $product_id );
  }

  function getCustomDiscountBreaks( $product_id ){
    $admin = new WC_CUSTOM_LOGO_ADMIN;
    return $admin->getCustomDiscountBreaks( $product_id );
  }

  function addFieldsBeforeCart(){
    global $product;

		$currency_symbol = get_woocommerce_currency_symbol();

		// OVERALL SETTINGS FOR THE PRODUCTS
		$allowed_settings = $this->getSettings( $product->id );
    $discount_break_values = $this->getCustomDiscountBreaks( $product->id );

		// LABEL DESIGNS
		$label_designs = wc_get_label_designs();
    $allowed_label_designs = array();

    // SIZES
		$sizes = getWCSizes();
		$allowed_sizes = array();

		foreach( $allowed_settings as $setting ){
			//echo $setting;
			if( isset( $label_designs[ $setting ] ) ){
				$allowed_label_designs[ $setting ] = $label_designs[ $setting ];
			}
			if( in_array( $setting, $sizes ) ){
				array_push( $allowed_sizes, $setting );
				//$allowed_sizes[ $setting ] = $label_designs[ $setting ];
			}
		}

    //print_r( $allowed_sizes );

    include( 'templates/fields-before-cart.php' );

  }

}

WC_PRODUCT_DATA::getInstance();
