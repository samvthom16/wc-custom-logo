<?php

  class WC_CUSTOM_LOGO_FRONTEND{

    function __construct(){
      add_action( 'wp_enqueue_scripts', array( $this, 'loadAssets' ) );
    }

    function loadAssets(){

      wp_enqueue_script( 'wc-logo', plugins_url( 'assets/js/main.js' , dirname(__FILE__) ), array('jquery'), WC_CUSTOM_LOGO_VERSION );
      wp_localize_script( 'wc-logo', 'wc_defaults', array(
        'logo_black'  =>  plugins_url( 'assets/images/logo-placeholder-black.png' , dirname(__FILE__) ),
        'logo_white'  =>  plugins_url( 'assets/images/logo-placeholder-white.png' , dirname(__FILE__) )
      ) );

      wp_enqueue_style( 'wc-logo-main', plugins_url( 'assets/css/main.css' , dirname(__FILE__) ), array(), WC_CUSTOM_LOGO_VERSION );
    }
  }

  new WC_CUSTOM_LOGO_FRONTEND;
