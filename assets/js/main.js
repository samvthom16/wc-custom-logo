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





WC_PRODUCT_DATA = {

  getDiscountInfo: function(){

    var qty = WC_PRODUCT_DATA.getQuantity();
    var data = window.discounts;

    // PUT THE DISCOUNT KEY BREAKS INTO AN ARRAY
    var breaks = [];
    for( const min in data ){
      breaks.push( parseInt( min ) );
    }

    var finalData = {
      key: 0,
      discount: 0,
      next: [
        {
          discount: data[ breaks[0] ],
          key: breaks[ 0 ]
        },
        {
          discount: data[ breaks[1] ],
          key: breaks[ 1 ]
        }
      ]
    };

    // SORT IN DESCENDING ORDER
    breaks.sort( function(a, b){return b-a} );

    for( var i=0; i<breaks.length; i++ ){
      if( qty >= breaks[i] ){

        // GETTING THE RIGHT DISCOUNT
        finalData.key = breaks[i];
        if( data.hasOwnProperty( breaks[i] ) ){
          finalData.discount = data[ breaks[i] ];
        }

        finalData.next = [];

        // FINDING THE NEXT DISCOUNT BREAK
        if( ( i-1 ) >= 0 ){
          finalData.next.push( {
            discount: data[ breaks[i-1] ],
            key: breaks[ i-1 ]
          } );
        }

        // FINDING THE NEXT DISCOUNT BREAK
        if( ( i-2 ) >= 0 ){
          finalData.next.push( {
            discount: data[ breaks[i-2] ],
            key: breaks[ i-2 ]
          } );
        }
        break;
      }
    }

    return finalData;
  },

  getCurrency: function(){
    return jQuery( '#product_total_price' ).data( 'currency' );
  },

  formatPrice: function( amount ){
    return WC_PRODUCT_DATA.getCurrency() + '' + parseFloat( amount ).toFixed( 2 );
  },

  insertPrice: function( class_selector, amount ){
    if( amount > 0 ){
      var html = WC_PRODUCT_DATA.formatPrice( amount );
      WC_PRODUCT_DATA.insertHTML( class_selector, html );
    }
  },

  insertHTML: function( class_selector, html ){
    var $el = jQuery( '#product_total_price' ).find( '.' + class_selector );
    $el.html( html );
  },

  getSelectedLabelDesignElements: function(){
    return jQuery( '[name="wc_custom_label_design[]"]:checked' );
  },

  getBasePrice: function( product_id ){
    var data = jQuery( 'form.variations_form' ).data( 'product_variations' );
    for( var i=0; i<data.length; i++ ){
      var product = data[i];
      if( product.variation_id == product_id ){
        return product.display_price;
      }
    }
  },

  getLabelDesignPrice: function(){
    var data = window.label_designs,
      price = 0;

    WC_PRODUCT_DATA.getSelectedLabelDesignElements().each( function(){
      var label_design_slug = jQuery( this ).val();
      price += data[ label_design_slug ].cost;
    } );
    return price;
  },

  getSizesPrice: function(){
    var data    = window.sizes_costs,
      total_qty = WC_PRODUCT_DATA.getQuantity(),
      price     = 0;

    for( var size in data ){
      var qty = jQuery( 'input[name="wc_custom_sizes[' + size + ']"]' ).val();
      if( qty ){
        price += data[ size ] * qty / total_qty;
      }
    }

    return price;
  },

  getRegularPrice: function(){
    var product_id  = jQuery( 'input.variation_id' ).val();
    var base_price = WC_PRODUCT_DATA.getBasePrice( product_id );
    var extra_price = WC_PRODUCT_DATA.getLabelDesignPrice() + WC_PRODUCT_DATA.getSizesPrice();

    //console.log(  );



    return base_price + extra_price;
  },

  getQuantity: function(){
    return jQuery( '[name=quantity]' ).val();
  },

  getEstimatedPrice: function(){
    return WC_PRODUCT_DATA.getRegularPrice() * WC_PRODUCT_DATA.getQuantity();
  },

  getDiscount: function(){
    return WC_PRODUCT_DATA.getDiscountInfo().discount;
  },

  getDiscountedPrice: function( discount ){
    var regular_price = WC_PRODUCT_DATA.getRegularPrice();
    return regular_price - ( regular_price * discount/100 );
  },

  getSalePrice: function(){
    var discount = WC_PRODUCT_DATA.getDiscount();
    return WC_PRODUCT_DATA.getDiscountedPrice( discount );
  },

  getTotalPrice: function(){
    return WC_PRODUCT_DATA.getSalePrice() * WC_PRODUCT_DATA.getQuantity();
  }

}



function wc_set_total_price(){
  var label_designs_no = WC_PRODUCT_DATA.getSelectedLabelDesignElements().length,
    discountData       = WC_PRODUCT_DATA.getDiscountInfo(),
    regular_price      = WC_PRODUCT_DATA.getRegularPrice(),
    sale_price         = WC_PRODUCT_DATA.getSalePrice(),
    estimated_price    = WC_PRODUCT_DATA.getEstimatedPrice(),
    total_price        = WC_PRODUCT_DATA.getTotalPrice();

  //console.log( discountData );

  if( regular_price != sale_price ){
    WC_PRODUCT_DATA.insertPrice( 'regular_price', regular_price );
    WC_PRODUCT_DATA.insertPrice( 'estimated_price', estimated_price );
  }

  WC_PRODUCT_DATA.insertPrice( 'sale_price', sale_price );
  WC_PRODUCT_DATA.insertPrice( 'total_price', total_price );

  WC_PRODUCT_DATA.insertHTML( 'qty', WC_PRODUCT_DATA.getQuantity() );
  WC_PRODUCT_DATA.insertHTML( 'discount', WC_PRODUCT_DATA.getDiscount() + '%' );


  if( discountData.next && discountData.next.length ){

    var discountedPrice1 = WC_PRODUCT_DATA.formatPrice( WC_PRODUCT_DATA.getDiscountedPrice( discountData.next[0]['discount'] ) );
    var discountedQty1 = discountData.next[0]['key'];
    var buyMoreText = 'Order ' + discountedQty1 + ' items and pay <span>' + discountedPrice1 + ' each</span>';

    if( discountData.next.length > 1 ){
      var discountedPrice2 = WC_PRODUCT_DATA.formatPrice( WC_PRODUCT_DATA.getDiscountedPrice( discountData.next[1]['discount'] ) );
      var discountedQty2 = discountData.next[1]['key'];
      buyMoreText += ', or ' + discountedQty2 + ' items and pay <span>' + discountedPrice2 + ' each</span>';
    }

    WC_PRODUCT_DATA.insertHTML( 'discount-text', buyMoreText );

    WC_PRODUCT_DATA.insertHTML( 'label_designs_no', label_designs_no );
  }


  if( total_price ){
    //jQuery( '#product_total_price' ).show();
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



  /* SHOW TOTAL PRODUCT PRICE */
  jQuery( 'input[name=quantity]' ).change( function() {
    wc_set_total_price();
  } );

  jQuery( 'input.variation_id' ).change( function(){
    wc_set_total_price();
  } );

  jQuery( '[name="wc_custom_label_design[]"]' ).change( function(){
    wc_set_total_price();
  } );

  jQuery( '#product_total_price' ).hide();





} );
