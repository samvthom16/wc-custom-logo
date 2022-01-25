<div id="wc_custom_logo_container" class="panel woocommerce_options_panel hidden">
  <div id='wc-customize-canvas'>
    <?php echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'aligncenter' ) );?>
    <div class='resize-logo'>
      <img src='<?php echo $custom_logo_placeholder_src;?>' />
    </div>
    <div class='wc-vertical-line'></div>
    <div class='wc-horizontal-line'></div>
  </div>
  <?php
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
</div>
