jQuery.fn.wc_logo_add = function( options ){
  var settings = jQuery.extend( {
    //stop: function(){}
  }, options );

  return this.each( function(){
    var $el       = jQuery( this ),
      post_id     = $el.data('post');

    $el.parent().addClass( 'wc-custom-logo-product-parent' );
    $el.parent().addClass( 'wc-custom-logo-product-parent-' + post_id );
  } );
};
