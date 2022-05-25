WC_CUSTOM_LOGO_FRONTEND = {

  setStyles: function(){
    if( localStorage.wc_custom_logo ){
      var $style = jQuery( document.createElement('style') );
      $style.html( '.wc-custom-logo-product-parent::after{ background-image: url( "' + localStorage.wc_custom_logo + '" ) !important;}' );
      $style.appendTo( 'body' );
      //preloaded.push( { id: 1, src: localStorage.wc_custom_logo } );
    }
  },

  setCustomLogo: function( attachment_src ){
    localStorage.wc_custom_logo = attachment_src;
  },

  redirect: function(){
    var redirect_url = jQuery( 'input[name=wc_custom_logo_redirect_url]' ).val();

    //console.log( redirect_url );


    if( redirect_url ){
      setTimeout( function() {
        location.href = redirect_url;
      }, 700 );
    }

  },

  uploadLogo: function( $form, callback = function(){} ){
    jQuery.ajax( {
      type        : 'POST',
      cache       : false,
      contentType : false,
      processData : false,
      url         : jQuery( 'input[name=wc_custom_logo_upload_url]' ).val(),
      data        : new FormData( $form ),
      success     : function( attachment_src ) {
        callback( attachment_src );
      }
    } );
  },

  removebg: function( attachment_src, callback ){

    console.log( 'inside removebg' );
    console.log( attachment_src );

    jQuery.ajax( {
      type        : 'GET',
      url         : jQuery( 'input[name=wc_custom_logo_removebg_url]' ).val(),
      data        : {
        img: attachment_src
      },
      success     : function( attachment_src ) {
        callback( attachment_src );
      }
    } );
  }
}

function wc_custom_logo_callback_completed( attachment_src ){
  var $submit_btn = jQuery( '[data-behaviour~=wc-custom-logo-form]' ).find( 'button[type=submit]' );
  $submit_btn.html( 'Uploaded' );

  WC_CUSTOM_LOGO_FRONTEND.setCustomLogo( attachment_src );
  console.log( attachment_src );

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

        console.log( attachment_src );

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

  jQuery('[data-behaviour~=wc-custom-sizes]').wc_custom_sizes();

  // DISABLE QUANTITY IN THE CART PAGE SO THAT THE SIZES QUANTITY ARE UNAFFECTED
  jQuery('.woocommerce-cart-form__cart-item .input-text.qty.text').attr('disabled', true );

} );
