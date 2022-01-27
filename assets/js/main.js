jQuery( document ).ready( function(){

  jQuery('[data-behaviour~=wc-custom-logo-product]').each( function(){
    var $el       = jQuery( this ),
      dimensions  = { top: 0, left: 0, width: 0 },
      post_id     = $el.data('post');

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


  } );

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
