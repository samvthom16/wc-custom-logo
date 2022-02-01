jQuery.fn.wc_logo_add = function( options ){
  var settings = jQuery.extend( {
    //stop: function(){}
  }, options );

  return this.each( function(){
    var $el       = jQuery( this ),
      dimensions  = { top: 0, left: 0, width: 0 },
      post_id     = $el.data('post');


    $el.parent().addClass( 'wc-custom-logo-product-parent' );
    $el.parent().addClass( 'wc-custom-logo-product-parent-' + post_id );

    /*
    function addNewLogo(){
      var $logo = jQuery( document.createElement('div') );
      $logo.addClass( 'wc-new-custom-logo' );
      $logo.appendTo( $el );

      if( dimensions.wc_logo_type === undefined ){
        dimensions.wc_logo_type = 'logo_black';
      }

      var $img = jQuery( document.createElement('img') );
      $img.attr( 'src', wc_defaults[dimensions.wc_logo_type] );
      $img.appendTo( $logo );

      //console.log( dimensions );

      $logo.css( {
        top: dimensions.wc_logo_top + '%',
        left: dimensions.wc_logo_left + '%',
        width: dimensions.wc_logo_width + '%',
        position: 'absolute'
      } );
    }

    if( post_id && window.browserData != undefined && window.browserData.wc_logo != undefined &&
      window.browserData.wc_logo[post_id] != undefined ){
      dimensions  = window.browserData.wc_logo[post_id];
      addNewLogo();
    }
    */
  } );
};

jQuery( document ).ready( function(){

  //console.log( HARUSHOP );
  //HARUSHOPMAIN
  
  if( localStorage.wc_custom_logo ){
    var $style = jQuery( document.createElement('style') );
    $style.html( '.wc-custom-logo-product-parent::after{ background-image: url( "' + localStorage.wc_custom_logo + '" );}' );
    $style.appendTo( 'body' );
  }


  jQuery('.input-images').imageUploader( {
    //imagesInputName:
    label: 'Upload Your Image 1200 x 1200 px',
    maxFiles: 1
  } );

  //jQuery( '.product-thumb-one' ).attr( 'data-behaviour', 'wc-custom-logo-product' );

  jQuery('[data-behaviour~=wc-custom-logo-product]').wc_logo_add();

  /*
  jQuery('.woocommerce-image-zoom').each( function(){
    var $el = jQuery( this ),
       img_src = $el.find('img').attr('src');
    $el.click( function( ev){
      jQuery.prettyPhoto.open(img_src,'','');
      //console.log( $el.html() );
    } );

  } );
  /*
  jQuery('.woocommerce-image-zoom').click( function( ev ){

    var $el = jQuery( ev.target );



    //console.log( jQuery( '#pp_full_res' ).html() );

    //jQuery(document).on('append', '#pp_full_res', function(){
      //console.log('open');
    //} );

    //jQuery('#pp_full_res').on('load', function(){
    //  console.log('open');
    //} );


  } );*/

} );
