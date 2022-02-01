<?php if( $attachment_src ):?>
  <script type='text/javascript'>
    localStorage.wc_custom_logo = '<?php echo $attachment_src;?>';
  </script>
<?php endif;?>
<div class='wc-logo-upload'>
  <form action="" method='POST' enctype="multipart/form-data">
    <?php wp_nonce_field( 'wc-logo-upload' );?>
    <div class='input-images'></div>
    <div class='label-button'>
      <label>Upload Design</label>
      <button>INSERT IMAGE</button>
    </div>
  </form>
</div>
