
function _WCGetPercentageLeftOfLogo( ui ){
  var image_width = jQuery('#wc-customize-canvas').width();
  return ( ui.position.left / image_width ) * 100;
}

function _WCGetPercentageTopOfLogo( ui ){
  var image_height = jQuery('#wc-customize-canvas').height();
  return ( ui.position.top / image_height ) * 100;
}

function WCUpdateLogo( ui ){
  if( ui.size != undefined ){
    var image_width = jQuery('#wc-customize-canvas').width();
    var percentage_width = ( ui.size.width/image_width ) * 100;
    jQuery( '#wc_logo_width' ).val( percentage_width );
  }

  jQuery( '#wc_logo_left' ).val( _WCGetPercentageLeftOfLogo( ui ) );
  jQuery( '#wc_logo_top' ).val( _WCGetPercentageTopOfLogo( ui ) );

  WCPositionResizeLogo();
}

function WCPositionResizeLogo(){
  var width = jQuery('#wc_logo_width').val();
  var top = jQuery('#wc_logo_top').val();
  var left = jQuery('#wc_logo_left').val();

  jQuery('#wc-customize-canvas .resize-logo').css( {
    top: top + '%',
    left: left + '%',
    width: width + '%'
  } );
}

jQuery( document ).ready( function(){
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

  jQuery( '#wc_logo_top, #wc_logo_left, #wc_logo_width' ).blur( function( ev ){
    WCPositionResizeLogo();
  } );

  // INIT FOR THE FIRST TIME
  WCPositionResizeLogo();
} );
