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
      wp_enqueue_script( 'wc-logo-resizable', plugins_url( 'wc-custom-logo/assets/js/resizable.min.js' , dirname(__FILE__) ) );
      wp_enqueue_script( 'wc-logo-draggable', plugins_url( 'wc-custom-logo/assets/js/draggable.min.js' , dirname(__FILE__) ) );
      wp_enqueue_script( 'wc-logo-admin', plugins_url( 'wc-custom-logo/assets/js/admin.js' , dirname(__FILE__) ) );

      wp_enqueue_style( 'wc-logo-admin', plugins_url( 'wc-custom-logo/assets/css/admin.css' , dirname(__FILE__) ) );
    }

    function productDataPanelsHTML(){

      global $post_id;
      $dimensions = array(
        'width' => 100,
        'top'   => 100,
        'left'  => 100
      );

      foreach( $dimensions as $key => $val ){
        $option = get_post_meta( get_the_ID(), 'wc_logo_' . $key, true );
        if( isset( $option ) ){
          $dimensions[ $key ] = $option;
        }
      }

      echo '<div id="wc_custom_logo_container" class="panel woocommerce_options_panel hidden">';

      echo "<div id='wc-customize-canvas'>";
      echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'aligncenter' ) );
      echo "<div id='resizeDiv'></div>";
      echo "</div>";

      woocommerce_wp_text_input( array(
    		'id'                => 'wc_logo_width',
    		'value'             => $dimensions['width'],
    		'label'             => 'Width',
    		'description'       => ''
    	) );

      woocommerce_wp_text_input( array(
    		'id'                => 'wc_logo_top',
    		'value'             => $dimensions['top'],
    		'label'             => 'Top Position',
    		'description'       => ''
    	) );

      woocommerce_wp_text_input( array(
    		'id'                => 'wc_logo_left',
    		'value'             => $dimensions['left'],
    		'label'             => 'Left Position',
    		'description'       => ''
    	) );

      echo '</div>';

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
