
function WCUpdateLogo( ui ){

  var image_width = jQuery('#wc-customize-canvas').width();
  var image_height = jQuery('#wc-customize-canvas').height();

  if( ui.size != undefined ){
    var percentage_width = ( ui.size.width/image_width ) * 100;
    jQuery( '#wc_logo_width' ).val( percentage_width );
  }

  var image_left = jQuery('#wc-customize-canvas').position().left;
  var percentage_left = ( ui.position.left / image_width ) * 100;
  jQuery( '#wc_logo_left' ).val( percentage_left );

  var image_top = jQuery('#wc-customize-canvas').position().top;
  var percentage_top = ( ui.position.top / image_height ) * 100;
  jQuery( '#wc_logo_top' ).val( percentage_top );

  WCPositionResizeLogo();

}

function WCPositionResizeLogo(){
  var width = jQuery('#wc_logo_width').val();
  var top = jQuery('#wc_logo_top').val();
  var left = jQuery('#wc_logo_left').val();

  jQuery('#resizeDiv').css( {
    top: top + '%',
    left: left + '%',
    width: width + '%',
    height: width + '%'
  } );
}

jQuery( document ).ready( function(){
  jQuery('#resizeDiv').draggable({
    containment: "parent",
    stop: function( event, ui ){
      WCUpdateLogo( ui );
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
