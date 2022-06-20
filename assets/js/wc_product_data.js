WC_PRODUCT_DATA = {

  /*
  * GET DISCOUNT INFORMATION
  * DISCOUNT AMOUNT
  * DISCOUNT BREAK POINT THAT WAS AVAILED
  * NEXT TWO BREAKPOINTS OF THE DISCOUNT
  */
  getDiscountInfo: function(){

    // GET THE QUANTITY OF THE CART ITEMS
    var qty = WC_PRODUCT_DATA.getQuantity();

    // GET DISCOUNT BREAKPOINT FROM THE SERVER
    var data = window.discounts;

    // PUT THE DISCOUNT KEY BREAKS INTO AN ARRAY
    var breaks = [];
    for( const min in data ){
      breaks.push( parseInt( min ) );
    }

    // DEFAULT DATA
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

    // ITERATE THROUGH BREAKPOINTS
    for( var i=0; i<breaks.length; i++ ){
      if( qty >= breaks[i] ){

        // GETTING THE RIGHT DISCOUNT
        finalData.key = breaks[i];
        if( data.hasOwnProperty( breaks[i] ) ){
          finalData.discount = data[ breaks[i] ];
        }

        // INIT THE NEXT BATCH
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

  /*
  * ADD CURRENCY TO THE AMOUNT
  * CONVERT TO FLOAT VALUE
  * FIX DECIMALS TO 2 PLACES ONLY
  */
  formatPrice: function( amount ){
    return WC_PRODUCT_DATA.getCurrency() + '' + parseFloat( amount ).toFixed( 2 );
  },

  /*
  * INSERTING DIFFERENT AMOUNT IN DIFFERENT HTML SELECTORS
  */
  insertPrice: function( class_selector, amount ){
    if( amount > 0 ){
      var html = WC_PRODUCT_DATA.formatPrice( amount );
      WC_PRODUCT_DATA.insertHTML( class_selector, html );
    }
  },

  /*
  * INSERTING HTML IN DIFFERENT HTML SELECTOR
  */
  insertHTML: function( class_selector, html ){
    var $el = jQuery( '#product_total_price' ).find( '.' + class_selector );
    $el.html( html );
  },

  getLabelDesignQuery: function(){
    return '[name="wc_custom_label_design[]"]';
  },

  /*
  * GET SELECTED LABEL DESIGN LABEL ELEMENTS
  * FRONT, BACK, LEFT CHEST
  */
  getSelectedLabelDesignElements: function(){
    return jQuery( WC_PRODUCT_DATA.getLabelDesignQuery() + ':checked' );
  },

  validateDesignLabels: function(){

    // RESET THE DISABLED ATTRIBUTES
    jQuery( WC_PRODUCT_DATA.getLabelDesignQuery() ).attr( 'disabled', false );

    var selectedLabelDesignElements = WC_PRODUCT_DATA.getSelectedLabelDesignElements();
    selectedLabelDesignElements.each( function(){
      var value = jQuery( this ).val();
      if( value == 'front' ){
        jQuery( WC_PRODUCT_DATA.getLabelDesignQuery() + '[value=chest]' ).attr( 'disabled', true );
      }
      if( value == 'chest' ){
        jQuery( WC_PRODUCT_DATA.getLabelDesignQuery() + '[value=front]' ).attr( 'disabled', true );
      }
    } );
  },

  /*
  * GET THE BASE PRICE OF THE PRODUCT THAT WAS SET IN THE BACKEND
  */
  getBasePrice: function( product_id ){
    var data = jQuery( 'form.variations_form' ).data( 'product_variations' );
    for( var i=0; i<data.length; i++ ){
      var product = data[i];
      if( product.variation_id == product_id ){
        return product.display_price;
      }
    }
  },

  /*
  * ADDITIONAL PRICE OF THE DESIGN LABELS
  * THAT IS CALCULATED BASED ON THE SELECTION OF THE USER
  */
  getLabelDesignPrice: function(){
    var data = window.label_designs,
      price = 0;

    WC_PRODUCT_DATA.getSelectedLabelDesignElements().each( function(){
      var label_design_slug = jQuery( this ).val();
      price += data[ label_design_slug ].cost;
    } );
    return price;
  },

  /*
  * ADDITIONAL PRICE OF THE SIZES
  * THAT IS CALCULATED BASED ON THE SELECTION OF THE USER
  */
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

  /*
  * REGULAR PRICE OF THE PRODUCT WHICH IS THE SUM OF
  * BASE PRICE & EXTRA PRICES BASED ON SELECTION
  * THIS IS DEVOID OF THE DISCOUNT AMOUNT
  */
  getRegularPrice: function(){
    var product_id  = jQuery( 'input.variation_id' ).val();
    var base_price = WC_PRODUCT_DATA.getBasePrice( product_id );
    var extra_price = WC_PRODUCT_DATA.getLabelDesignPrice() + WC_PRODUCT_DATA.getSizesPrice();
    return base_price + extra_price;
  },

  /*
  * ELEMENT THAT HOLDS THE QUANTITY DROPDOWN
  */
  getQuantityElement: function(){
    return jQuery( '[name=quantity]' );
  },

  /*
  * GET QUANTITY OF THE PRODUCT THAT HAS BEEN SELECTED
  */
  getQuantity: function(){
    return WC_PRODUCT_DATA.getQuantityElement().val();
  },

  /*
  * MANIPULATE THE QUANTITY
  * USED WHEN THE QUANTITY WITHIN THE SIZES TABLE CHANGES
  */
  setQuantity: function( qty ){
    WC_PRODUCT_DATA.getQuantityElement().val( qty );
  },

  /*
  * ESTIMATED PRICE: TOTAL SUM OF PRODUCTS SELECTED
  * DEVOID THE DISCOUNT AMOUNT
  */
  getEstimatedPrice: function(){
    return WC_PRODUCT_DATA.getRegularPrice() * WC_PRODUCT_DATA.getQuantity();
  },

  /*
  * GET DISCOUNT PERCENTAGE BASED ON THE BRACKET THE QUANTITY FALLS
  */
  getDiscount: function(){
    return WC_PRODUCT_DATA.getDiscountInfo().discount;
  },

  /*
  * GET DISCOUNT AMOUNT BASED ON THE BRACKET THE QUANTITY FALLS
  * HAS ALSO BEEN USED TO PROVIDE FUTURE DISCOUNTED RATES
  * TO ENCOURAGE USERS TO INCREASE QUANTITY
  */
  getDiscountedPrice: function( discount ){
    var regular_price = WC_PRODUCT_DATA.getRegularPrice();
    return regular_price - ( regular_price * discount/100 );
  },

  /*
  * GET SALE PRICE WHICH IS THE DISCOUNTED PRICE
  * ONLY USED TO CALCULATE FINAL SALE PRICE FOR 1 PRODUCT
  */
  getSalePrice: function(){
    var discount = WC_PRODUCT_DATA.getDiscount();
    return WC_PRODUCT_DATA.getDiscountedPrice( discount );
  },

  /*
  * FINAL SALE PRICE FOR ALL PRODUCTS
  */
  getTotalPrice: function(){
    return WC_PRODUCT_DATA.getSalePrice() * WC_PRODUCT_DATA.getQuantity();
  },

  getSizesTableElement: function(){
    return jQuery('[data-behaviour~=wc-custom-sizes]');
  },

  initSizesTable: function(){
    var $table  = WC_PRODUCT_DATA.getSizesTableElement();
    if( $table.length ){

      $table.find('input[type=number]').change( function(){
        WC_PRODUCT_DATA.calculateSizeQuantity();
      } );

      jQuery('.quantity').addClass( 'hide' );
      WC_PRODUCT_DATA.calculateSizeQuantity();
    }
  },

  getSizeQuantity: function(){
    var $table  = WC_PRODUCT_DATA.getSizesTableElement();
    var qty_value = 0;

    $table.find('input[type=number]').each( function(){
      var $qty_input = jQuery( this );
      qty_value += parseInt( $qty_input.val() );
    } );
    return qty_value;
  },

  /*
  * MANIPULATES THE OVERALL QUANTITY WHEN
  * INDIVIDUAL SIZES QUANTITY ARE CHANGED
  */
  calculateSizeQuantity: function(){

    var qty_value = WC_PRODUCT_DATA.getSizeQuantity();

    if( qty_value < 1 ) qty_value = 1;  // min value of qty should be 1

    WC_PRODUCT_DATA.setQuantity( qty_value );
    WC_PRODUCT_DATA.setTotalPrice();
  },

  /*
  * MAIN FUNCTION THAT IS TRIGGERED
  * WHENEVER USER SELECTION CHANGES
  * SHOWS ALL THE PRICES INCLUDING DYNAMIC
  * DISCOUNT BREAKPOINTS
  */
  setTotalPrice: function(){
    var label_designs_no = WC_PRODUCT_DATA.getSelectedLabelDesignElements().length,
      discountData       = WC_PRODUCT_DATA.getDiscountInfo(),
      regular_price      = WC_PRODUCT_DATA.getRegularPrice(),
      sale_price         = WC_PRODUCT_DATA.getSalePrice(),
      estimated_price    = WC_PRODUCT_DATA.getEstimatedPrice(),
      total_price        = WC_PRODUCT_DATA.getTotalPrice();

    // SHOW ONLY IF REGULAR PRICE IS MORE THAN THE SALE PRICE
    if( regular_price != sale_price ){
      WC_PRODUCT_DATA.insertPrice( 'regular_price', regular_price );
      WC_PRODUCT_DATA.insertPrice( 'estimated_price', estimated_price );
    }

    // SALE PRICE & ESTIMATED PRICE
    WC_PRODUCT_DATA.insertPrice( 'sale_price', sale_price );
    WC_PRODUCT_DATA.insertPrice( 'total_price', total_price );

    // SET QUANTITY & DISCOUNT & LABEL DESIGNS
    WC_PRODUCT_DATA.insertHTML( 'qty', WC_PRODUCT_DATA.getSizeQuantity() );
    WC_PRODUCT_DATA.insertHTML( 'discount', WC_PRODUCT_DATA.getDiscount() + '%' );
    WC_PRODUCT_DATA.insertHTML( 'label_designs_no', label_designs_no );


    // DYNAMIC DISCOUNT BREAKS
    if( discountData.next && discountData.next.length ){

      // 1ST BATCH OF DISCOUNT BREAK
      var discountedPrice1 = WC_PRODUCT_DATA.formatPrice( WC_PRODUCT_DATA.getDiscountedPrice( discountData.next[0]['discount'] ) );
      var discountedQty1 = discountData.next[0]['key'];
      var buyMoreText = 'Order ' + discountedQty1 + ' items and pay <span>' + discountedPrice1 + ' each</span>';

      // 2ND BATCH OF DISCOUNT BREAK
      if( discountData.next.length > 1 ){
        var discountedPrice2 = WC_PRODUCT_DATA.formatPrice( WC_PRODUCT_DATA.getDiscountedPrice( discountData.next[1]['discount'] ) );
        var discountedQty2 = discountData.next[1]['key'];
        buyMoreText += ', or ' + discountedQty2 + ' items and pay <span>' + discountedPrice2 + ' each</span>';
      }

      // FINAL INSERTION OF DYNAMIC TEXT
      WC_PRODUCT_DATA.insertHTML( 'discount-text', buyMoreText );
    }

    // ONLY SHOW THE PRICE BREAKUP IF SOME PRICE IS PRESENT: ENSURE IF THE CALCULATION WAS RIGHT
    if( total_price ){
      jQuery( '#product_total_price' ).show();
    }
  },

  /*
  * INITIALISATION FUNCTION TO TRIGGER FOR THE FIRST TIME
  */
  init: function(){

    // INIT IF SIZES TABLE IS PRESENT
    WC_PRODUCT_DATA.initSizesTable();

    // TRIGGER WHEN QUANTITY DROPDOWN CHANGES
    jQuery( 'input[name=quantity]' ).change( function() {
      WC_PRODUCT_DATA.setTotalPrice();
    } );

    // TRIGGER WHEN COLOR SWATCH IS SELECTED
    jQuery( 'input.variation_id' ).change( function(){
      WC_PRODUCT_DATA.setTotalPrice();
    } );

    // TRIGGER WHEN A LABEL DESIGN IS SELECTED
    jQuery( '[name="wc_custom_label_design[]"]' ).change( function(){
      WC_PRODUCT_DATA.setTotalPrice();
      WC_PRODUCT_DATA.validateDesignLabels();
    } );
    WC_PRODUCT_DATA.validateDesignLabels();

    // HIDE TOTAL PRODUCT PRICE FOR THE INITAL TIME
    jQuery( '#product_total_price' ).hide();
  }

}
