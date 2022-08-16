<?php

  class WC_CUSTOM_LOGO_ADMIN{

    function __construct(){
      add_action( 'admin_enqueue_scripts', array( $this, 'loadAssets' ) );

      add_action( 'admin_footer', function(){
        if( is_admin( 'post.php') ){
          global $post;
          wc_inject_script( $post->ID );
        }
        wc_inject_all_styles();
      } );

      add_filter("attachment_fields_to_edit", array( $this, "addToAttachmentFields" ), 1, 2);
      add_action( 'woocommerce_product_after_variable_attributes', array( $this, "addToVariableMetabox"), 10, 3 );

      /*
      * SAVE PRODUCT PANEL DATA
      */
      add_filter( 'woocommerce_product_data_tabs',  function( $tabs ){
        $tabs['wc_custom_logo'] = array(
		      'label'    => 'Settings',
		      'target'   => 'misha_product_data',
		      'class'    => array(),
		      'priority' => 21,
	       );
	       return $tabs;
      } );
      add_action( 'woocommerce_product_data_panels', array( $this, 'showProductPanels' ) );
      add_action('woocommerce_process_product_meta', function(){

        $checklist = array();
        if( isset( $_POST[ 'wc_custom_settings' ] ) ){
          $checklist = $_POST[ 'wc_custom_settings' ];
        }
        update_post_meta( get_the_ID(), 'wc_custom_settings', $checklist );

      } );
      add_action( 'attachment_fields_to_save', array( $this, 'saveProductDataPanels') );

      /*
      * WooCommerce SETTINGS FOR CUSTOM LOGO
      */
      add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
      add_action( 'woocommerce_settings_tabs_custom_logo', function(){
        woocommerce_admin_fields( $this->wc_settings_custom_logo() );
      } );
      add_action( 'woocommerce_update_options_custom_logo', function(){
        woocommerce_update_options( $this->wc_settings_custom_logo() );
      } );
      add_action( 'woocommerce_settings_tabs_discount_breaks', function(){
        woocommerce_admin_fields( $this->wc_settings_discount_breaks() );
      } );
      add_action( 'woocommerce_update_options_discount_breaks', function(){
        woocommerce_update_options( $this->wc_settings_discount_breaks() );
      } );

    }

    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['custom_logo'] = __( 'Custom Logo', 'woocommerce-settings-custom-logo' );
        $settings_tabs['discount_breaks'] = __( 'Discount Breaks', 'woocommerce-settings-custom-logo' );
        return $settings_tabs;
    }

    function wc_settings_discount_breaks(){
      $settings = array(
        'section_title' => array(
          'name'  => 'Discount Breakpoints',
          'type'  => 'title',
          'id'    => 'wc_settings_tab_custom_logo_section_title'
        )
      );

      $discount_table = getWCDiscountBreaks();
      foreach( $discount_table as $key ){
        $settings[ 'discount_' . $key ] = array(
          'name' => "Discount for $key",
          'type' => 'text',
          'desc' => "Discount percentage for items when the total quantity >= $key",
          'id'   => "wc_settings_discount[$key]"
        );
      }

      $settings[ 'section_end' ] = array(
        'type'  => 'sectionend',
        'id'    => 'wc_settings_tab_custom_logo_section_end'
      );

      return apply_filters( 'wc_settings_tab_discount_breaks_settings', $settings );
    }


    function wc_settings_custom_logo() {
      $settings = array(
        'section_title' => array(
          'name'     => __( 'Custom Logo Settings', 'woocommerce-settings-custom-logo' ),
          'type'     => 'title',
          'desc'     => '',
          'id'       => 'wc_settings_tab_custom_logo_section_title'
        ),
        'remove_bg' => array(
          'name' => __( 'Remove BG API Key', 'woocommerce-settings-custom-logo' ),
          'type' => 'text',
          'desc' => __( 'API key used for removing background colors from the custom logos', 'woocommerce-settings-custom-logo' ),
          'id'   => 'wc_settings_tab_custom_logo_api_key'
        ),
        'section_end' => array(
          'type'  => 'sectionend',
          'id'    => 'wc_settings_tab_custom_logo_section_end'
        )
      );

      $settings['section_title1'] = array(
        'name'  => 'Extra Prices For Sizes',
        'type'  => 'title',
      );
      $sizes = getWCSizes();
      foreach( $sizes as $size ){
        $settings[ 'size_' . $size ] = array(
          'name' => "Cost of $size",
          'type' => 'text',
          'desc' => "Additional price for items with sizes $size",
          'id'   => "wc_settings_size[$size]"
        );
      }
      $settings['section_end1'] = array(
        'type'  => 'sectionend',
      );

      $settings['section_title2'] = array(
        'name'  => 'Extra Prices For Label Placements',
        'type'  => 'title',
      );
      $labels = getWCLabelDesigns();

      foreach( $labels as $key => $label ){
        $settings[ 'label_' . $key ] = array(
          'name' => "Cost of $label",
          'type' => 'text',
          'desc' => "Additional price for items with $label placement",
          'id'   => "wc_settings_labels[$key]"
        );
      }
      $settings['section_end2'] = array(
        'type'  => 'sectionend',
      );

      return apply_filters( 'wc_settings_tab_custom_logo_settings', $settings );
    }

    function loadAssets(){

      wp_enqueue_script( 'wc-logo-common', plugins_url( 'assets/js/common.js' , dirname(__FILE__) ), array( 'jquery' ), WC_CUSTOM_LOGO_VERSION );

      wp_enqueue_script( 'wc-logo-admin', plugins_url( 'assets/js/admin.js' , dirname(__FILE__) ), array( 'jquery', 'wc-logo-common' ), WC_CUSTOM_LOGO_VERSION );
      wp_localize_script( 'wc-logo-admin', 'wc_defaults', array(
        'logo_black'  =>  plugins_url( 'assets/images/logo-placeholder-black.png' , dirname(__FILE__) ),
        'logo_white'  =>  plugins_url( 'assets/images/logo-placeholder-white.png' , dirname(__FILE__) )
      ) );
      wp_enqueue_style( 'wc-logo-admin', plugins_url( 'assets/css/admin.css' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
      wp_enqueue_style( 'wc-logo-common', plugins_url( 'assets/css/common.css' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
    }


    /*
    * SAVE EACH DIMENSION TO THE PRODUCT META ITSELF
    */
    function saveProductDataPanels(){
      $option_name = 'wc_logo_enabled_ids';
      $ids = get_option( $option_name, array() );

      $id = 'wc_logo_dimensions';
      if( isset( $_POST[ $id ] ) ){
        update_post_meta( get_the_ID(), $id, $_POST[ $id ] );
        array_push( $ids, get_the_ID() );
        update_option( $option_name, $ids );
      }
    }

    function getEditAttachmentLink( $post_id ){
      return admin_url() . "post.php?post=" . $post_id . "&action=edit";
    }

    function getCustomiseBtnHTML( $post_id ){
      $link = $this->getEditAttachmentLink( $post_id );
      return '<a class="button button-html" href="'.$link.'" target="_blank">Customise (Add Logo)</a>';
    }

    /*
    * THIS METABOX APPEARS INSIDE ATTACHMENT FIELDS WHEN THE IMAGE IS SELECTED THROUGH THE POP UP
    */
    function addToAttachmentFields( $form_fields, $post ){
      if( substr($post->post_mime_type, 0, 5) == 'image' ){
        $form_fields["wc_custom_logo"]["input"] = "html";
        $form_fields["wc_custom_logo"]["html"] = $this->getCustomiseBtnHTML( $post->ID );
      }
      return $form_fields;
    }

    /*
    * THIS METABOX APPEARS INSIDE VARIATIONS PRODUCT IN THE SINGLE PRODUCT PAGE
    */
    function addToVariableMetabox( $loop, $variation_data, $variation ){
      $thumbnail_id = get_post_thumbnail_id( $variation );
      if( $thumbnail_id ){
        echo $this->getCustomiseBtnHTML( $thumbnail_id );
      }
    }

    function getSettings( $product_id ){
      $checklist_values = get_post_meta( $product_id, 'wc_custom_settings', true );
      if( !is_array( $checklist_values ) ) $checklist_values = array();
      return $checklist_values;
    }

    function showProductPanels(){

      $checklist_values = $this->getSettings( get_the_ID() );

      $options = array(
        'discount'  => 'Discount breakpoints',
      );

      $sizes = getWCSizes();
      foreach( $sizes as $size ){
        $options[ $size ] = 'Size: ' . $size;
      }

      $labels_titles = getWCLabelDesigns();
      foreach( $labels_titles as $slug => $label_title ){
        $options[ $slug ] = 'Logo placement: ' . $label_title;
      }

      include( 'templates/product-data-settings.php' );

    }


  }

  new WC_CUSTOM_LOGO_ADMIN;
