jQuery( document ).ready( function(){

  jQuery('[data-behaviour~=wc-custom-logo-product]').each( function(){
    var $el       = jQuery( this ),
      post_id     = $el.data('post'),
      dimensions  = window.browserData.wc_logo[post_id];

    var $logo = jQuery( document.createElement('div') );
    $logo.addClass( 'wc-new-custom-logo' );
    $logo.appendTo( $el );

    var $img = jQuery( document.createElement('img') );
    $img.attr( 'src', 'http://localhost/anurag/wp-content/plugins/wc-custom-logo/assets/images/custom-logo-placeholder.png' );
    $img.appendTo( $logo );

    $logo.css( {
      top: dimensions.top + '%',
      left: dimensions.left + '%',
      width: dimensions.width + '%',
      position: 'absolute'
    } );

  } );

} );
