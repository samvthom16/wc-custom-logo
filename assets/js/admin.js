








jQuery.fn.wc_logo_resize = function( options ){

  var settings = jQuery.extend( {
    stop: function(){}
  }, options );

  return this.each( function(){
    var $el = jQuery( this ),
      startX, startY, startWidth, startHeight;

    function doDrag(e) {
      $el[0].style.width = (startWidth + e.clientX - startX) + 'px';
      $el[0].style.height = (startHeight + e.clientY - startY) + 'px';
    }

    function stopDrag(e) {
      document.documentElement.removeEventListener( 'mousemove', doDrag, false );
      document.documentElement.removeEventListener( 'mouseup', stopDrag, false );

      var ui = {
        size: {
          width: parseInt( document.defaultView.getComputedStyle( $el[0] ).width ),
        },
        position: {
          left: parseInt( document.defaultView.getComputedStyle( $el[0] ).left ),
          top: parseInt( document.defaultView.getComputedStyle( $el[0] ).top )
        }
      };

      settings.stop( ui );

    }

    var $resizer = jQuery( document.createElement('div') );
    $resizer.addClass( 'resizer' );
    $resizer.appendTo( $el );

    $resizer.mousedown( function( ev ){
      startX = ev.clientX;
      startY = ev.clientY;
      startWidth = parseInt( document.defaultView.getComputedStyle( $el[0] ).width, 10 );
      //startHeight = parseInt( document.defaultView.getComputedStyle( $el[0] ).height, 10 );
      document.documentElement.addEventListener( 'mousemove', doDrag, false);
      document.documentElement.addEventListener( 'mouseup', stopDrag, false);
    } );
  } );
};

jQuery.fn.wc_logo_customise = function( options ) {

  var settings = jQuery.extend( {
    id: 0
  }, options );

  return this.each(function() {

    /*
    * CREATES THE NEW CUSTOM LOGO FOR RESIZE & DRAG
    */
    function createResizeLogo(){
      var $resizeLogo = jQuery( document.createElement('div') );
      var $img = jQuery( document.createElement( 'img' ) );
      $resizeLogo.addClass( 'resize-logo' );
      $img.attr( 'src', wc_defaults.logo_black );
      $img.appendTo( $resizeLogo );
      return $resizeLogo;
    }

    function updateLogoType(){
      var logo_type = jQuery( '#wc_logo_type' ).val();
      $parent.find( '.resize-logo img' ).attr( 'src', wc_defaults[logo_type] );
    }

    function getPercentageLeftOfLogo( ui ){
      var image_width = $parent.width();
      return ( ui.position.left / image_width ) * 100;
    }

    function getPercentageTopOfLogo( ui ){
      var image_height = $parent.height();
      return ( ui.position.top / image_height ) * 100;
    }

    function getFormValueFromDB( id, defaultValue ){
      var valueFromDB = defaultValue;
      if( window.browserData != undefined && window.browserData[ 'wc_logo' ] != undefined && window.browserData[ 'wc_logo' ][ settings.id ] != undefined ){
        valueFromDB = window.browserData[ 'wc_logo' ][ settings.id ][ id ];
      }
      return valueFromDB;
    }

    function createFormElement( id, label ){
      var $container = jQuery( document.createElement( 'div' ) );
      $container.addClass( 'wc-logo-field' );

      var $label = jQuery( document.createElement( 'label' ) );
      $label.html( label );
      $label.appendTo( $container );
      return $container;
    }

    function createInputElement( id, label, defaultValue ){
      $container = createFormElement( id, label, defaultValue );

      var $input = jQuery( document.createElement('input') );
      $input.attr( 'id', id );
      $input.attr( 'name', 'wc_logo_dimensions[' + id + ']' );
      $input.attr( 'type', 'number' );
      $input.attr( 'step', 'any' );
      $input.val( getFormValueFromDB( id, defaultValue ) );
      $input.appendTo( $container );
      $input.blur( function( ev ){
        repositionLogo();
      } );
      return $container;
    }

    function createLogosDropdown( id, label, defaultValue ){
      $container = createFormElement( id, label, defaultValue );

      var $select = jQuery( document.createElement('select') );
      $select.html( "<option value='logo_black'>Black Logo</option><option value='logo_white'>White Logo</option>" );
      $select.attr( 'id', id );
      $select.attr( 'name', 'wc_logo_dimensions[' + id + ']' );
      $select.val( getFormValueFromDB( id, defaultValue ) );
      $select.appendTo( $container );

      $select.change( function(){
        updateLogoType();
      } );

      return $container;
    }

    var $el       = jQuery( this ),
      $parent     = $el.parent(),
      $grandparent = $parent.parent(),
      $resizeLogo = createResizeLogo();

    /*
    * REPOSITION LOGO
    */
    function repositionLogo(){
      var width = jQuery('#wc_logo_width').val();
      var top = jQuery('#wc_logo_top').val();
      var left = jQuery('#wc_logo_left').val();

      $resizeLogo.css( {
        top: top + '%',
        left: left + '%',
        width: width + '%'
      } );
    }

    function updateLogo( ui ){
      if( ui.size != undefined ){
        var image_width = $parent.width();
        var percentage_width = ( ui.size.width/image_width ) * 100;
        jQuery( '#wc_logo_width' ).val( percentage_width );
      }

      jQuery( '#wc_logo_left' ).val( getPercentageLeftOfLogo( ui ) );
      jQuery( '#wc_logo_top' ).val( getPercentageTopOfLogo( ui ) );

      repositionLogo();
    }

    function createHelperDiv( newClass ){
      var $div = jQuery( document.createElement( 'div' ) );
      $div.addClass( newClass );
      $div.appendTo( $parent );
    }

    /*
    * INITIALISATION FUNCTION
    */
    function init(){
      createLogosDropdown( 'wc_logo_type', 'Choose Logo', 'logo_black' ).appendTo( $grandparent );
      createInputElement( 'wc_logo_width', 'Width of New Logo', 10 ).appendTo( $grandparent );
      createInputElement( 'wc_logo_top', 'Top Position of New Logo', 10 ).appendTo( $grandparent );
      createInputElement( 'wc_logo_left', 'Left Position of New Logo', 10 ).appendTo( $grandparent );

      createHelperDiv( 'wc-vertical-line' );
      createHelperDiv( 'wc-horizontal-line' );

      $parent.attr( 'data-behaviour', 'wc-logo-customize' );

      $resizeLogo.appendTo( $parent );

      $resizeLogo.draggable({
        containment: "parent",
        drag: function( event, ui ){
          var percentage_left = getPercentageLeftOfLogo( ui );
          var percentage_top = getPercentageTopOfLogo( ui );

          if( percentage_left > 40 && percentage_left < 60 ){
            $parent.find('.wc-vertical-line').show();
          }

          if( percentage_top > 40 && percentage_top < 60 ){
            $parent.find('.wc-horizontal-line').show();
          }
        },
        stop: function( event, ui ){
          updateLogo( ui );
          $parent.find('.wc-vertical-line').hide();
          $parent.find('.wc-horizontal-line').hide();
        }
      }).wc_logo_resize( { stop: updateLogo } );
      repositionLogo();
      updateLogoType();
    }


    init();
  } );
};

jQuery( document ).ready( function(){

  var url_string = window.location;
  var url = new URL(url_string);
  var post_id = url.searchParams.get( "post" );

  if( post_id ){
    jQuery('body.post-type-attachment .wp_attachment_image img').wc_logo_customise( {
      id: post_id
    } );
  }


  /*
  jQuery('#wc-customize-canvas .resize-logo').draggable({
    containment: "parent",
    drag: function( event, ui ){
      var percentage_left = _WCGetPercentageLeftOfLogo( ui );
      if( percentage_left > 40 && percentage_left < 60 ){
        jQuery('#wc-customize-canvas .wc-vertical-line').show();
      }

      var percentage_top = _WCGetPercentageTopOfLogo( ui );
      if( percentage_top > 40 && percentage_top < 60 ){
        jQuery('#wc-customize-canvas .wc-horizontal-line').show();
      }
    },
    stop: function( event, ui ){
      WCUpdateLogo( ui );
      jQuery('#wc-customize-canvas .wc-vertical-line').hide();
      jQuery('#wc-customize-canvas .wc-horizontal-line').hide();
    }
  }).resizable({
    delay       : 300,
    distance    : 10,
    containment: "parent",
    aspectRatio : 1,
    minWidth    : 80,
    maxWidth    : 300,
    stop: function( event, ui ){
      WCUpdateLogo( ui );
    }
  });
  */



  // INIT FOR THE FIRST TIME
  //WCPositionResizeLogo();
} );
