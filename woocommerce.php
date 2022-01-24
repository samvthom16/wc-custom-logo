<?php

function wpdocs_selectively_enqueue_admin_script( $hook ) {
  wp_enqueue_script( 'custom-js', plugins_url( 'wc-custom-logo/assets/js/resizable.min.js' , dirname(__FILE__) ) );
}
add_action( 'admin_enqueue_scripts', 'wpdocs_selectively_enqueue_admin_script' );

add_action('admin_head', 'misha_css_icon');
function misha_css_icon(){
  echo '<style>
    #woocommerce-product-data ul.wc-tabs li.misha_options.misha_tab a:before{ content: "\f100"; }
    #wc-customize-canvas{ text-align: center; }
    #wc-customize-canvas img{ max-width: 100%; height: auto;}
    #resizeDiv{ border: #000 solid 1px; width: 100px; height: 100px; position: absolute; left:25%; top: 25%;}
  </style>';
}

add_action( 'admin_footer', function(){
  ?>
  <script>
    jQuery('#resizeDiv').draggable({
      drag: function( event, ui ){
        WCUpdateLogo( ui );
      }
    }).resizable({
      containment: "#wc-customize-canvas img",
      aspectRatio : 1,
      minWidth    : 80,
      maxWidth    : 300,
      resize: function( event, ui ){
        WCUpdateLogo( ui );
      }
    });

    function WCUpdateLogo( ui ){
      if( ui.size != undefined ){
        var image_width = jQuery('#wc-customize-canvas').width();
        //console.log( jQuery('#wc-customize-canvas img').width() );
        //console.log( image_width );
        //var percentage_width = ( ui.size.width/image_width ) * 100;
        jQuery( '#wc_logo_width' ).val( ui.size.width );
      }
      jQuery( '#wc_logo_top' ).val( ui.position.top );
      jQuery( '#wc_logo_left' ).val( ui.position.left );
    }
  </script>
  <?php
} );

/*
 * Tab
 */
add_filter('woocommerce_product_data_tabs', 'misha_product_settings_tabs' );
function misha_product_settings_tabs( $tabs ){
  $tabs['misha'] = array(
		'label'    => 'Customize',
		'target'   => 'misha_product_data',
		'class'    => array(),
		'priority' => 21,
	);
	return $tabs;

}

/*
 * Tab content
 */
add_action( 'woocommerce_product_data_panels', 'misha_product_panels' );
function misha_product_panels(){

	echo '<div id="misha_product_data" class="panel woocommerce_options_panel hidden">';

  global $post_id;


  echo "<div id='wc-customize-canvas'>";
  echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'aligncenter' ) );
  echo "<div id='resizeDiv'></div>";
  echo "</div>";

  $dimensions = array(
    'width' => 100,
    'top'   => 100,
    'left'  => 100
  );

  foreach( $dimensions as $key => $val ){
    $option = get_post_meta( get_the_ID(), 'wc_logo_' . $key, true );
    if( isset( $option ) ){
      $dimensions[ $key ] = $option;
    }
  }

  woocommerce_wp_text_input( array(
		'id'                => 'wc_logo_width',
		'value'             => $dimensions['width'],
		'label'             => 'Width',
		'description'       => ''
	) );

  woocommerce_wp_text_input( array(
		'id'                => 'wc_logo_top',
		'value'             => $dimensions['top'],
		'label'             => 'Top Position',
		'description'       => ''
	) );

  woocommerce_wp_text_input( array(
		'id'                => 'wc_logo_left',
		'value'             => $dimensions['left'],
		'label'             => 'Left Position',
		'description'       => ''
	) );

  ?>
  <style>
    #resizeDiv{
      width: <?php _e( $dimensions['width'] );?>px;
      height: <?php _e( $dimensions['width'] );?>px;
      left: <?php _e( $dimensions['left'] );?>px;
      top: <?php _e( $dimensions['top'] );?>px;
    }
  </style>
  <?php



	echo '</div>';

}

add_action('woocommerce_process_product_meta', function(){

  $ids_arr = array( 'wc_logo_width', 'wc_logo_top', 'wc_logo_left' );

  foreach( $ids_arr as $id ){
    $val = 0;
    if( isset( $_POST[ $id ] ) ){
      $val = $_POST[ $id ];
    }
    update_post_meta( get_the_ID(), $id, $val );
  }

} );
