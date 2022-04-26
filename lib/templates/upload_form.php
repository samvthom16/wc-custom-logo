<?php if( $attachment_src ):?>
  <script type='text/javascript'>
    localStorage.wc_custom_logo = '<?php echo $attachment_src;?>';
  </script>
<?php endif;?>
<div class='wc-logo-upload'>
  <form action="" method='POST' enctype="multipart/form-data" data-behaviour='wc-custom-logo-form'>
    <?php wp_nonce_field( 'wc-logo-upload' );?>
    <div class='input-images'></div>
    <div class='label-button'>
      <h4>Upload Design</h4>
      <label style='font-weight:normal; margin-bottom: 20px;line-height: 1.2'>
        <input type='checkbox' name='wc_custom_logo_remove_flag' />
        Remove background color
      </label>
      <button type='submit' disabled='disabled'>ADD IMAGE TO PRODUCTS</button>
    </div>
    <input type='hidden' name='wc_custom_logo_removebg_url' value='<?php echo admin_url( 'admin-ajax.php?action=wc_custom_logo_removebg' );?>' />
    <input type='hidden' name='wc_custom_logo_upload_url' value='<?php echo admin_url( 'admin-ajax.php?action=wc_custom_logo_upload' );?>' />
    <input type='hidden' name='wc_custom_logo_redirect_url' value='<?php echo $atts['redirect'];?>' />
  </form>
</div>
<?php if( $_POST && $_FILES ):?>
  <script type='text/javascript'>
    //location.href = '<?php echo $atts['redirect'];?>';
  </script>
<?php endif;?>
