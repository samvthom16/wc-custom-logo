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

function wc_get_variation_price( variation_id ){
  var data = jQuery( 'form.variations_form' ).data( 'product_variations' );
  for( var i=0; i<data.length; i++ ){
    var product = data[i];
    if( product.variation_id == variation_id ){
      return product.display_price;
    }
  }
}

function wc_get_label_designs_price(){
  var data = window.label_designs,
    price = 0;

  jQuery( '[name="wc_custom_label_design[]"]:checked' ).each( function(){
    var label_design_slug = jQuery( this ).val();
    price += data[ label_design_slug ].cost;
  } );
  return price;
}

function wc_get_discount( qty ){
  var data = window.discounts;
  var discount = 0
  for( const min in data ){
    if( qty >= parseInt( min ) && data[min] > discount ) discount = data[min];
  }
  return discount;
}

function wc_set_total_price(){
  var qty     = jQuery( '[name=quantity]' ).val(),
    currency = jQuery( '#product_total_price' ).data( 'currency' ),
    discount = wc_get_discount( qty ),
    var_id  = jQuery( 'input.variation_id' ).val();

  function showPrice( $el, amount ){
    if( amount > 0 ){
      amount = parseFloat( amount ).toFixed( 2 );
      $el.html( currency + '' + amount );
    }
  }

  if( '' != var_id && qty ) {
    var base_price = wc_get_variation_price( var_id );
    var extra_price = wc_get_label_designs_price();
    var regular_price = base_price + extra_price;

    var sale_price = regular_price - ( regular_price * discount/100 );

    var estimated_price = regular_price * qty;
    var price = sale_price * qty;

    if( regular_price != sale_price ){
      showPrice( jQuery( '#product_total_price span.regular_price' ), regular_price );
      showPrice( jQuery( '#product_total_price span.estimated_price' ), estimated_price );
    }

    showPrice( jQuery( '#product_total_price span.sale_price' ), sale_price );
    showPrice( jQuery( '#product_total_price span.total_price' ), price );



    //console.log( wc_get_discount( qty ) );

  }

  if( price ){
    //jQuery( '#product_total_price span.base_price' ).html( sale_price );
    //jQuery( '#product_total_price span.sale_price' ).html( discounted_price );

    jQuery( '#product_total_price span.discount' ).html( discount + '%' );
    jQuery( '#product_total_price span.qty' ).html( qty );

    jQuery( '#product_total_price' ).show();
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
