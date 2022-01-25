<?php

  class WC_CUSTOM_LOGO_ADMIN{

    function __construct(){
      add_action( 'admin_enqueue_scripts', array( $this, 'loadAssets' ) );

      /*
      * ADD ANOTHER TAB TO THE PRODUCT DATA SECTION
      */
      add_filter( 'woocommerce_product_data_tabs', function( $tabs ){
        $tabs['wc_custom_logo'] = array(
      		'label'    => 'Customize',
      		'target'   => 'wc_custom_logo_container',
      		'class'    => array(),
      		'priority' => 21,
      	);
      	return $tabs;
      } );

      /*
      * HTML FOR THE PRODUCT PANELS
      */
      add_action( 'woocommerce_product_data_panels', array( $this, 'productDataPanelsHTML' ) );

      /*
      * SAVE PRODUCT PANEL DATA
      */
      add_action('woocommerce_process_product_meta', array( $this, 'saveProductDataPanels') );
    }

    function loadAssets(){
      wp_enqueue_script( 'wc-logo-resizable', plugins_url( 'assets/js/resizable.min.js' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
      wp_enqueue_script( 'wc-logo-draggable', plugins_url( 'assets/js/draggable.min.js' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
      wp_enqueue_script( 'wc-logo-admin', plugins_url( 'assets/js/admin.js' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );

      wp_enqueue_style( 'wc-logo-admin', plugins_url( 'assets/css/admin.css' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
    }

    function productDataPanelsHTML(){
      $post_id = get_the_ID();
      $dimensions = wc_get_dimensions( $post_id );
      $custom_logo_placeholder_src = plugins_url( 'assets/images/custom-logo-placeholder.png' , dirname(__FILE__) );
      include "templates/product-data-panels.php";
    }

    /*
    * SAVE EACH DIMENSION TO THE PRODUCT META ITSELF
    */
    function saveProductDataPanels(){
      $ids_arr = array( 'wc_logo_width', 'wc_logo_top', 'wc_logo_left' );
      foreach( $ids_arr as $id ){
        $val = 0;
        if( isset( $_POST[ $id ] ) ){
          $val = $_POST[ $id ];
        }
        update_post_meta( get_the_ID(), $id, $val );
      }
    }
  }

  new WC_CUSTOM_LOGO_ADMIN;
