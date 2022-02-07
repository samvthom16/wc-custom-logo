<?php

  class WC_CUSTOM_LOGO_FRONTEND{

    function __construct(){
      add_action( 'wp_enqueue_scripts', array( $this, 'loadAssets' ) );

      add_shortcode( 'wc_logo_upload', array( $this, 'uploadHTML' ) );

      add_action( 'wp_ajax_nopriv_wc_custom_logo_upload', array( $this, 'ajaxUploadForm' ) );
      add_action( 'wp_ajax_wc_custom_logo_upload', array( $this, 'ajaxUploadForm' ) );
    }

    function loadAssets(){

      wp_enqueue_script( 'wc-image-uploader', plugins_url( 'assets/js/image-uploader.min.js' , dirname(__FILE__) ), array('jquery'), WC_CUSTOM_LOGO_VERSION );

      wp_enqueue_script( 'wc-stber', plugins_url( 'assets/js/stber-hacks.js' , dirname(__FILE__) ), array( 'jquery', 'wc-logo' ), WC_CUSTOM_LOGO_VERSION );

      wp_enqueue_script( 'wc-logo-common', plugins_url( 'assets/js/common.js' , dirname(__FILE__) ), array( 'jquery' ), WC_CUSTOM_LOGO_VERSION );

      wp_enqueue_script( 'wc-logo', plugins_url( 'assets/js/main.js' , dirname(__FILE__) ), array( 'jquery', 'wc-image-uploader', 'wc-logo-common' ), WC_CUSTOM_LOGO_VERSION );
      wp_localize_script( 'wc-logo', 'wc_defaults', array(
        'logo_black'  => plugins_url( 'assets/images/logo-placeholder-black.png' , dirname(__FILE__) ),
        'logo_white'  => plugins_url( 'assets/images/logo-placeholder-white.png' , dirname(__FILE__) ),
        'ids'         => get_option( 'wc_logo_enabled_ids', array() )
      ) );

      wp_enqueue_style( 'wc-image-uploader', plugins_url( 'assets/css/image-uploader.min.css' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
      wp_enqueue_style( 'wc-logo-main', plugins_url( 'assets/css/main.css' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
      wp_enqueue_style( 'wc-logo-common', plugins_url( 'assets/css/common.css' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
    }

    function validateFiles(){
      foreach( $_FILES as $key => $fileobject ){
        if( isset( $fileobject['name'] ) && is_array( $fileobject['name'] ) && count( $fileobject['name'] ) ){
          $total_files = count( $fileobject['name'] );
          for( $i=0; $i<$total_files; $i++ ){
            $temp_file = array();

            //$fields = array( 'name', 'type', 'tmp_name', 'error', 'size' );

            $fields = array_keys( $fileobject );
            foreach( $fields as $field ){
              if( isset( $fileobject[ $field ] ) && is_array( $fileobject[ $field ] ) && isset( $fileobject[ $field ][ $i ] ) ){
                $temp_file[ $field ] = $fileobject[ $field ][ $i ];
              }
            }
            $_FILES[ $key."_".$i ] = $temp_file;
          }
          unset( $_FILES[ $key ] );
        }
      }
    }

    function handleMediaUpload( $data = array() ){
  		if( is_array( $data ) ){
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        foreach( $data as $key => $value ){
          $attachment_id = media_handle_upload( $key, 0, array( 'test_form'=> false ) );
          return $attachment_id;
          /*
          if( is_wp_error( $attachment_id ) ){
            //print_r( $attachment_id );
          }
          */
  			}
      }
  	}

    function getAttachmentSrc( $attachment_id ){
      $attachment_src = wp_get_attachment_image_src( $attachment_id, 'full' );
      if( is_array( $attachment_src ) && count( $attachment_src ) ){
        return $attachment_src[0];
      }
      return '';
    }

    function uploadHTML( $atts ){

      $atts = shortcode_atts( array(
        'redirect' => '',
      ), $atts, 'wc_logo_upload' );

      $attachment_id = 0;
      $attachment_src = '';

      if( $_POST && $_FILES ){

        // VERIFY NONCE
        wp_verify_nonce( $_REQUEST['_wpnonce'], 'wc-logo-upload' );

        // VALIDATE FILES
        $this->validateFiles();

        // UPLOAD MEDIA
        $attachment_id = $this->handleMediaUpload( $_FILES );
        $attachment_src = $this->getAttachmentSrc( $attachment_id );
      }

      ob_start();
      include( 'templates/upload_form.php' );
      return ob_get_clean();
    }

    function ajaxUploadForm(){

      $attachment_id = 0;
      $attachment_src = '';

      if( $_POST && $_FILES ){

        // VERIFY NONCE
        wp_verify_nonce( $_REQUEST['_wpnonce'], 'wc-logo-upload' );

        // VALIDATE FILES
        $this->validateFiles();

        // UPLOAD MEDIA
        $attachment_id = $this->handleMediaUpload( $_FILES );
        $attachment_src = $this->getAttachmentSrc( $attachment_id );
      }

      echo $attachment_src;
      
      wp_die();
    }

  }

  new WC_CUSTOM_LOGO_FRONTEND;
