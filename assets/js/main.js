function wc_custom_logo_callback_completed( attachment_src ){
  var $submit_btn = jQuery( '[data-behaviour~=wc-custom-logo-form]' ).find( 'button[type=submit]' );
  $submit_btn.html( 'Uploaded' );

  WC_CUSTOM_LOGO_FRONTEND.setCustomLogo( attachment_src );
  //console.log( attachment_src );

  WC_CUSTOM_LOGO_FRONTEND.redirect();
}

jQuery( document ).ready( function(){

  //console.log( HARUSHOP );
  //HARUSHOPMAIN

  var preloaded = [];

  WC_CUSTOM_LOGO_FRONTEND.setStyles();

  jQuery( '[data-behaviour~=wc-custom-logo-form]' ).each( function(){

    //console.log( 'testing mode 1.0' );

    var $el       = jQuery( this );

    $el.submit( function( ev ){

      ev.preventDefault();

      var $submit_btn = $el.find( 'button[type=submit]' );
      $submit_btn.html( 'Uploading ...' );

      var removebg_flag = jQuery( 'input[name=wc_custom_logo_remove_flag]:checked' ).length;

      WC_CUSTOM_LOGO_FRONTEND.uploadLogo( $el[0], function( attachment_src ){

        //console.log( attachment_src );

        // CHECK REMOVE BG FLAG
        if( removebg_flag ){

          $submit_btn.html( 'Removing background color ...' );

          // REMOVE BG - AJAX REQUEST
          WC_CUSTOM_LOGO_FRONTEND.removebg(
            attachment_src,
            function( attachment_src ){
              wc_custom_logo_callback_completed( attachment_src );
            }
          )
        }
        else{
          wc_custom_logo_callback_completed( attachment_src );
        }
      } );
    } );
  } );


  // INIT THE IMAGE UPLOADER
  jQuery('.input-images').imageUploader( {
    preloaded : preloaded,
    label     : 'Upload Your Image 1200 x 1200 px',
    maxFiles  : 1
  } );

  // ENABLE THE FORM SUBMIT WHEN THE IMAGE IS SELECTED
  jQuery( 'input[type=file]' ).on( 'change', function(){
    jQuery( '.wc-logo-upload button[type=submit]' ).attr( 'disabled', false );
  } );

  // ADD THE LOGO FROM THE LOCAL STORAGE ON ALL LOOGOS
  jQuery('[data-behaviour~=wc-custom-logo-product]').wc_logo_add();

  //jQuery('[data-behaviour~=wc-custom-sizes]').wc_custom_sizes();

  // DISABLE QUANTITY IN THE CART PAGE SO THAT THE SIZES QUANTITY ARE UNAFFECTED
  jQuery('.woocommerce-cart-form__cart-item .input-text.qty.text').attr('disabled', true );

  WC_PRODUCT_DATA.init();

} );
