<style>
  .wc-custom-logo-product-parent-<?php _e( $post_id );?>::after{
    width   : <?php _e( $dimensions['wc_logo_width'] );?>%;
    top     : <?php _e( $dimensions['wc_logo_top'] );?>%;
    left    : <?php _e( $dimensions['wc_logo_left'] );?>%;
    height  : <?php _e( $dimensions['wc_logo_width'] );?>%;
    <?php if( is_admin() ):?>
    background-image: url( '<?php _e( wc_default_logo_placeholder( $dimensions['wc_logo_type'] ) );?>' );
    <?php endif;?>
  }
</style>
