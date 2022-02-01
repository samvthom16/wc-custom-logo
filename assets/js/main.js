jQuery( document ).ready( function(){

  //console.log( HARUSHOP );
  //HARUSHOPMAIN

  var preloaded = [];

  if( localStorage.wc_custom_logo ){
    var $style = jQuery( document.createElement('style') );
    $style.html( '.wc-custom-logo-product-parent::after{ background-image: url( "' + localStorage.wc_custom_logo + '" ) !important;}' );
    $style.appendTo( 'body' );

    preloaded.push( { id: 1, src: localStorage.wc_custom_logo } );
  }


  jQuery('.input-images').imageUploader( {
    preloaded : preloaded,
    label     : 'Upload Your Image 1200 x 1200 px',
    maxFiles  : 1
  } );

  jQuery('[data-behaviour~=wc-custom-logo-product]').wc_logo_add();

} );
