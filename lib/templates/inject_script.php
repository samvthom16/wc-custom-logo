<script type="text/javascript">
  if( window.browserData === undefined || window.browserData[ 'wc_logo' ] === undefined || window.browserData[ 'wc_logo' ][ <?php _e( $post->ID )?> ] === undefined ){
    var data = window.browserData = window.browserData || {};
    window.browserData[ 'wc_logo' ] = window.browserData[ 'wc_logo' ] || {};
    window.browserData[ 'wc_logo' ][ <?php _e( $post->ID )?> ] = <?php echo wp_json_encode( $dimensions );?>;
  }
</script>
