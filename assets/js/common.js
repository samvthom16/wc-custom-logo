jQuery.fn.wc_logo_add = function( options ){
  var settings = jQuery.extend( {
    //stop: function(){}
  }, options );

  return this.each( function(){
    var $el       = jQuery( this ),
      cart        = $el.data('cart'),
      post_id     = $el.data('post');



    if( !cart ){
      $el.parent().addClass( 'wc-custom-logo-product-parent' );
      $el.parent().addClass( 'wc-custom-logo-product-parent-' + post_id );
    }
    else{
      if( $el.closest( '.cart_item' ).find( '.wc-cart-logo img' ).length ){



        //var style = window.getComputedStyle( $el.parent()[0], '::after' );
        //var bg_image = style.getPropertyValue( 'background-image' );

        //console.log( bg_image );

        //console.log( $el.parent().parent().find( '.wc-custom-logo-product-parent::after' ).length );

        var logo_src = $el.closest( '.cart_item' ).find( '.wc-cart-logo img' ).attr('src');

        $el.parent()[0].style.setProperty( 'wc_logo', logo_src );

        //console.log( logo_src );

        //style.setPropertyValue( 'background-image', "url('" + logo_src + "')" );
      }
    }
  } );
};
