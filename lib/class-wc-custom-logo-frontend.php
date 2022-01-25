<?php

  class WC_CUSTOM_LOGO_FRONTEND{

    function __construct(){
      add_action( 'wp_enqueue_scripts', array( $this, 'loadAssets' ) );
    }

    function loadAssets(){

      //wp_enqueue_script( 'wc-logo', plugins_url( 'assets/js/main.js' , dirname(__FILE__) ), array('jquery'), WC_CUSTOM_LOGO_VERSION );

      //wp_enqueue_style( 'wc-logo-admin', plugins_url( 'assets/css/admin.css' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
    }
  }

  new WC_CUSTOM_LOGO_FRONTEND;
