<?php

// HACKS TO SHOW IN THE PRODUCT PAGE AND CART

namespace WC_CUSTOM_LOGO;

class WC_CUSTOM_CART extends WC_BASE{

  function __construct(){
    add_action( 'woocommerce_before_calculate_totals', array( $this, 'calculateTotals' ), 10, 1 );

    // Display custom data on cart and checkout page.
    add_filter( 'woocommerce_get_item_data', array( $this, 'get_wc_cart_item_data' ), 25, 2 );

    // Add data to cart item
    add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 25, 2 );
  }

  function add_cart_item_data( $cart_item_data, $product_id ) {
  	$keys = array(
      'wc_custom_logo_image_src',
      'wc_custom_label_design',
      'wc_custom_sizes',
      'wc_custom_text'
    );
  	foreach( $keys as $key ){
  		if( isset( $_POST[$key] ) ) $cart_item_data[$key] = $_POST[$key];
  	}
  	return $cart_item_data;
  }

  function get_wc_cart_item_data ( $cart_data, $cart_item ) {

    if( isset( $cart_item['wc_custom_label_design'] ) && is_array( $cart_item['wc_custom_label_design'] ) ){
  		$selected_label_designs = $cart_item['wc_custom_label_design'];
  		$label_designs = wc_get_label_designs();
  		$selected_labels = array();
  		foreach( $selected_label_designs as $slug ){
  			if( isset( $label_designs[ $slug ] ) ){
  				array_push( $selected_labels, $label_designs[ $slug ]['label'] );
  			}
  		}

  		if( count( $selected_labels ) ){
  			$cart_data[] = array(
  				'name' 	=> 'Placements',
  				'value'	=> implode( ', ', $selected_labels )
  			);
  		}
  	}

  	if( isset( $cart_item['wc_custom_sizes'] ) && is_array( $cart_item['wc_custom_sizes'] ) ){
  		$sizes = array();
  		foreach( $cart_item['wc_custom_sizes'] as $label => $qty ){
  			if( $qty > 0 ){
  				array_push( $sizes, $label . "($qty)" );
  			}
  		}
  		if( count( $sizes ) ){
  			$cart_data[] = array(
  				'name' 	=> 'Sizes',
  				'value'	=> implode( ', ', $sizes )
  			);
  		}

  	}


    // SHOW IF THERE IS A DISCOUNT AVAILABLE
    $avail_discount = $this->getAvailableDiscount( $cart_item['product_id'], $cart_item['quantity'] );
    if( $avail_discount ){
      $cart_data[] = array(
    		'name' 	=> 'Discount',
    		'value'	=> $avail_discount . '%'
    	);
    }


    $key = 'wc_custom_logo_image_src';
    $image_id = ( isset( $cart_item['data'] ) && isset( $cart_item['data']->get_data()['image_id'] ) ) ? $cart_item['data']->get_data()['image_id'] : 0;
    if( !empty( $cart_item[ $key ] ) && $image_id ):?>
  		<p style='margin-top: 20px; text-decoration: underline; font-size:small;'>
        <a data-resize='1' data-rel='prettyPhoto' href='<?php echo $cart_item[ $key ];?>'>With Custom Logo</a>
      <p>
    <?php endif;


    if( isset( $cart_item[ 'wc_custom_text' ] ) && !empty( $cart_item[ 'wc_custom_text' ] ) ){
      $cart_data[] = array(
        'name'  => 'Custom Text',
        'value' => $cart_item['wc_custom_text']
      );
    }

    return $cart_data;
  }

  function getAvailableDiscount( $product_id, $quantity ){
    $discount_table = WC_PRODUCT_DATA::getCustomDiscountBreaks( $product_id );
    //$discount_table = wc_get_global_discounts_table();
    $avail_discount = 0;
    foreach( $discount_table as $min => $discount ){
      if( $quantity >= $min ){
        $avail_discount = $discount;
        break;
      }
    }
    return $avail_discount;
  }

  function calculateTotals( $cart_obj ){
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
  		return;
  	}

  	// Iterate through each cart item
  	foreach( $cart_obj->get_cart() as $key => $value ) {

      // CHECK IF THE CUSTOM LABEL DESIGN HAS BEEN SELECTED
  		if( isset( $value['wc_custom_label_design'] ) && is_array( $value['wc_custom_label_design'] ) ) {
  			$label_designs = wc_get_label_designs();
  			$price = $value['data']->get_price();

        // ITERATE THROUGH EACH SELECTED DESIGN AND ADD TO THE BASE PRICE
  			foreach( $value['wc_custom_label_design'] as $slug ){
  				if( isset( $label_designs[ $slug ] ) ){
  					$price += $label_designs[ $slug ]['cost'];
  				}
  			}
  			$value['data']->set_price( ( $price ) );
  		}

      // CHECK IF THE CUSTOM SIZE HAS BEEN SELECTED
      if( isset( $value['wc_custom_sizes'] ) && is_array( $value['wc_custom_sizes'] ) ){
        $sizes_costs = getWCSizesCosts();
        $price = $value['data']->get_price();

        // ITERATE THROUGH EACH SELECTED SIZE AND ADD TO THE BASE PRICE
        foreach( $value['wc_custom_sizes'] as $slug => $size_qty ){
          if( $size_qty && isset( $sizes_costs[ $slug ] ) ){
            $price += $sizes_costs[ $slug ] * $size_qty / $value['quantity'];
          }
        }
        $value['data']->set_price( ( $price ) );
      }

      // APPLY AVAILABLE DISCOUNT FROM THE TABLE
      //print_r(  );
  		$avail_discount = $this->getAvailableDiscount( $value['product_id'], $value['quantity'] );
      $price = $value['data']->get_price();
  		$value['data']->set_price( $price - ( $price * $avail_discount/100 ) );

  	}
  }

}

WC_CUSTOM_CART::getInstance();
