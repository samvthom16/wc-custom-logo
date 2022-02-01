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
      add_action( 'attachment_fields_to_save', array( $this, 'saveProductDataPanels') );

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


  }

  new WC_CUSTOM_LOGO_ADMIN;
