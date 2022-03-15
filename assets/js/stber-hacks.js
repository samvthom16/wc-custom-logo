/*
* HACKING THE PARENT THEME JS FUNCTION OF PRETTY PHOTO
*/
jQuery( document ).ready( function(){

  HARU.base.prettyPhoto = function(){
    jQuery( "a[data-rel^='prettyPhoto']").each( function(){
      var $el = jQuery( this ),
        $parent = $el.parent();

      $el.prettyPhoto( {
          hook:'data-rel',
          social_tools:'',
          animation_speed:'normal',
          theme:'light_square',
          allow_resize: false,
          changepicturecallback: function(){
            var post_id = $parent.find( 'img[data-behaviour~=wc-custom-logo-product]' ).data( 'post' );

            // ADD THE REQUIRED ATTRIBUTES
            jQuery( '#fullResImage' ).attr( 'data-behaviour', 'wc-custom-logo-product' );
            jQuery( '#fullResImage' ).attr( 'data-post', post_id );

            jQuery( '#fullResImage' ).wc_logo_add();
          }
      } );
    } );
  };

  HARUSHOPMAIN.woocommerce.quickView = function(){
    jQuery('a.quickview').each( function(){
      var $el   = jQuery( this ),
        post_id = $el.closest( 'li.product' ).find( 'img[data-behaviour~=wc-custom-logo-product]' ).data( 'post' );


        $el.prettyPhoto({
            deeplinking: false,
            opacity: 1,
            social_tools: false,
            default_width: 900,
            default_height: 600,
            theme: 'pp_woocommerce',
            changepicturecallback : function() {

              jQuery( '#pp_full_res [data-behaviour~=wc-custom-logo-product]' ).wc_logo_add();

                jQuery('.pp_inline').find('form.variations_form').wc_variation_form();
                jQuery('.pp_inline').find('form.variations_form .variations select').change();
                jQuery('body').trigger('wc_fragments_loaded');

                jQuery('.pp_woocommerce').addClass('loaded');

                var product_images    = jQuery("#product-images1", ".popup-product-quick-view-wrapper");
                var product_thumbnails    = jQuery("#product-thumbnails1", ".popup-product-quick-view-wrapper");

                // Maybe use settimeout to fix
                product_images.slick();
                product_thumbnails.slick();

                // Re run addToCartVariation to make it work
                HARUSHOPMAIN.woocommerce.addToCartVariation();

                jQuery(document).on('change','.variations_form .variations select,.variations_form .variation_form_section select,div.select',function() {
                    var variation_form   = jQuery(this).closest( '.variations_form' );
                    var current_settings = {},
                        reset_variations = variation_form.find( '.reset_variations' );
                    variation_form.find('.variations select,.variation_form_section select' ).each( function() {
                        // Encode entities
                        var value = jQuery(this).val();

                        // Add to settings array
                        current_settings[ jQuery(this).attr( 'name' ) ] = jQuery(this).val();
                    });

                    variation_form.find('.variation_form_section div.select input[type="hidden"]' ).each( function() {
                        // Encode entities
                        var value = jQuery(this).val();

                        // Add to settings array
                        current_settings[ jQuery(this).attr( 'name' ) ] = jQuery(this).val();
                    });

                    var all_variations = variation_form.data( 'product_variations' );
                    var variation_id   = 0;
                    var match          = true;

                    for (var i = 0; i < all_variations.length; i++) {
                        match                     = true;
                        var variations_attributes = all_variations[i]['attributes'];
                        for(var attr_name in variations_attributes) {
                            var val1 = variations_attributes[attr_name];
                            var val2 = current_settings[attr_name];
                            if (val1 == undefined || val2 == undefined ) {
                                match = false;
                                break;
                            }
                            if (val1.length == 0) {
                                continue;
                            }

                            if (val1 != val2) {
                                match = false;
                                break;
                            }
                        }
                        if (match) {
                            variation_id = all_variations[i]['variation_id'];
                            break;
                        }
                    }

                    if (variation_id > 0) {
                        var index = parseInt(jQuery('a[data-variation_id*="|'+variation_id+'|"]','#product-images1').data('index'),10) ;
                        if (!isNaN(index) ) {
                            product_images.slick('slickGoTo', index, true);
                        }
                    }
                });
            }
        } );

    } );

  }
} );
