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
		'lib/class-wc-custom-cart.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
  }

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
		return array(
			'front' => array(
				'label' => 'Front (+ $3.00)',
				'cost'	=> 3
			),
			'back' 	=> array(
				'label' => 'Back (+ $3.00)',
				'cost'	=> 3
			),
			'chest' 	=> array(
				'label' => 'Left Chest (+ $2.50)',
				'cost'	=> 2.5
			),
		);
	}

	function wc_get_discounts_table(){
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
	}

	function getWCSettings( $product_id ){
		$admin = new WC_CUSTOM_LOGO_ADMIN;
		return $admin->getSettings( $product_id );
	}

	function getWCSizes(){
		return array(
			'YS', 'YM', 'YL', 'YXL', 'S', 'M', 'L', 'XL', '2XL', '3XL'
		);
	}

	function getWCSizesCosts(){
		return array(
			'2XL' => 2,
			'3XL'	=> 3
		);
	}


	// Displaying the checkboxes
	add_action( 'woocommerce_before_add_to_cart_button', 'add_fields_before_add_to_cart' );
	function add_fields_before_add_to_cart( ) {

		$currency_symbol = get_woocommerce_currency_symbol();

		global $product;

		// OVERALL SETTINGS FOR THE PRODUCTS
		$allowed_settings = getWCSettings( $product->id );
		//print_r( $allowed_settings );

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


	?>
		<div class="wc-custom-logo-extras">

			<?php if( count( $allowed_label_designs ) ):?>
			<div style='margin:20px 0 40px;'>
				<p>Select one or more placements for your design:</p>
				<ul style='list-style:none; padding-left:0;'>
				<?php foreach( $allowed_label_designs as $slug => $label_design ):?>
					<li>
						<label>
							<input type='checkbox' name='wc_custom_label_design[]' value='<?php echo $slug;?>' />
							<span><?php echo $label_design['label'];?></span>
						</label>
					</li>
				<?php endforeach;?>
				</ul>
			</div>
			<?php endif;?>

			<?php if( count( $allowed_sizes ) ):?>
			<table class='wc-table' data-behaviour='wc-custom-sizes'>
				<tr>
					<?php foreach( $allowed_sizes as $size ):?>
						<th><?php echo $size;?></th>
					<?php endforeach;?>
				</tr>
				<tr>
					<?php foreach( $allowed_sizes as $size ): $name = "wc_custom_sizes[ $size ]";?>
						<td><input type='number' name='<?php echo $name;?>' value='0' step='1' min='0' /></td>
					<?php endforeach;?>
				</tr>
			</table>
			<?php endif;?>

			<script>
				window.label_designs = <?php echo json_encode( $label_designs );?>;
				window.discounts = <?php echo json_encode( wc_get_discounts_table() )?>;
				window.sizes_costs = <?php echo json_encode( getWCSizesCosts() )?>;
			</script>


			<input type='hidden' name='wc_custom_logo_image_src' value='<?php echo WC()->session->get( 'wc_custom_logo' );?>' />

			<div id='product_total_price' style='margin-bottom:20px;' data-currency='<?php echo $currency_symbol;?>'>
				<div>
					<p class='no-margin-bottom font-big'><span class='regular_price'></span>&nbsp;<span class='sale_price purple'></span> <span class='purple'>each</span></p>
					<p class='no-margin-bottom'><span class='estimated_price'></span>&nbsp;<span class='total_price purple'></span> <span class='purple'>total</span> with <span class='discount'>0%</span> Discount</p>
					<ul>
						<li><span class="qty">1</span> total items</li>
						<li><span class="label_designs_no">0</span> print area</li>
					</ul>
				</div>

				<div class='buy-more'>
					<h5>BUY MORE, SAVE MORE</h5>
					<p class='discount-text'>Order 15 items and pay $12.91 each</p>
					<p class='no-margin-bottom'><a href='' style='text-decoration: underline;'>Money Saving Tips</a></p>
				</div>

			</div>



		</div>
		<?php
	}

// Add data to cart item
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data', 25, 2 );
function add_cart_item_data( $cart_item_data, $product_id ) {
	$keys = array( 'wc_custom_logo_image_src', 'wc_custom_label_design', 'wc_custom_sizes' );
	foreach( $keys as $key ){
		if( isset( $_POST[$key] ) ) $cart_item_data[$key] = $_POST[$key];
	}
	return $cart_item_data;
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
