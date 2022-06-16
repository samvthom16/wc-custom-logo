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
