function setStyles(){

}

jQuery( document ).ready( function(){

  //console.log( HARUSHOP );
  //HARUSHOPMAIN

  var preloaded = [];

  if( localStorage.wc_custom_logo ){
    var $style = jQuery( document.createElement('style') );
    $style.html( '.wc-custom-logo-product-parent::after{ background-image: url( "' + localStorage.wc_custom_logo + '" ) !important;}' );
    $style.appendTo( 'body' );

    //preloaded.push( { id: 1, src: localStorage.wc_custom_logo } );
  }

  jQuery( '[data-behaviour~=wc-custom-logo-form]' ).each( function(){
    var $el       = jQuery( this ),
    url           = jQuery( 'input[name=wc_custom_logo_upload_url]' ).val(),
    redirect_url  = jQuery( 'input[name=wc_custom_logo_redirect_url]' ).val();

    $el.submit( function( ev ){
      ev.preventDefault();

      var formdata = new FormData( $el[0] );

      var $submit_btn = $el.find( 'button[type=submit]' );
      $submit_btn.html( 'Uploading ...' );

      jQuery.ajax( {
        type        : 'POST',
        cache       : false,
        contentType : false,
        processData : false,
        url         : url,
        data        : formdata,
        success     : function( attachment_src ) {
          localStorage.wc_custom_logo = attachment_src;

          console.log( attachment_src );

          if( redirect_url ){
            setTimeout( function() {
              location.href = redirect_url;
            }, 700 );
          }
        }
      } );

      console.log( 'submit' );
    } );
  } );



  jQuery('.input-images').imageUploader( {
    preloaded : preloaded,
    label     : 'Upload Your Image 1200 x 1200 px',
    maxFiles  : 1
  } );

  jQuery('[data-behaviour~=wc-custom-logo-product]').wc_logo_add();

} );
