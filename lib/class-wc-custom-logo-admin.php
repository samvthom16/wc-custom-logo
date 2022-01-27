<?php

  class WC_CUSTOM_LOGO_ADMIN{

    function __construct(){
      add_action( 'admin_enqueue_scripts', array( $this, 'loadAssets' ) );

      add_action( 'admin_footer', function(){
        if( is_admin( 'post.php') ){
          global $post;
          $dimensions = get_post_meta( $post->ID, 'wc_logo_dimensions', true );
          include( 'templates/inject_script.php' );
        }
      } );

      add_filter("attachment_fields_to_edit", array( $this, "addToAttachmentFields" ), 1, 2);
      add_action( 'woocommerce_product_after_variable_attributes', array( $this, "addToVariableMetabox"), 10, 3 );

      /*
      * SAVE PRODUCT PANEL DATA
      */
      add_action( 'attachment_fields_to_save', array( $this, 'saveProductDataPanels') );

    }

    function loadAssets(){
      //wp_enqueue_script( 'wc-logo-resizable', plugins_url( 'assets/js/resizable.min.js' , dirname(__FILE__) ), array( 'jquery', 'jquery-ui-core' ), WC_CUSTOM_LOGO_VERSION );
      //wp_enqueue_script( 'wc-logo-draggable', plugins_url( 'assets/js/draggable.min.js' , dirname(__FILE__) ), array( 'jquery', 'jqueryui' ), WC_CUSTOM_LOGO_VERSION );
      wp_enqueue_script( 'wc-logo-admin', plugins_url( 'assets/js/admin.js' , dirname(__FILE__) ), array( 'jquery' ), WC_CUSTOM_LOGO_VERSION );
      wp_localize_script( 'wc-logo-admin', 'wc_defaults', array( 'logo' =>   plugins_url( 'assets/images/custom-logo-placeholder.png' , dirname(__FILE__) ) ) );
      wp_enqueue_style( 'wc-logo-admin', plugins_url( 'assets/css/admin.css' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
    }


    /*
    * SAVE EACH DIMENSION TO THE PRODUCT META ITSELF
    */
    function saveProductDataPanels(){
      $id = 'wc_logo_dimensions';
      if( isset( $_POST[ $id ] ) ){
        update_post_meta( get_the_ID(), $id, $_POST[ $id ] );
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
