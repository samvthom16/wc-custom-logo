<div id="misha_product_data" class="panel woocommerce_options_panel hidden">
  <p class="form_field">Select to enable the following options:</p>
  <?php foreach( $options as $slug => $option ):?>
  <p class='form_field' style='margin:0; line-height:0.75;'>
    <input type='checkbox' name='wc_custom_settings[]' <?php if( in_array( $slug, $checklist_values ) ) _e( "checked='checked'" );?> value='<?php echo $slug;?>' />
    <?php echo $option;?>
  </p>
  <?php endforeach;?>

  <div style='padding-left: 10px;margin-top: 20px;'>
    <?php
      $min_qty = isset( $checklist_values['min_qty'] ) && intval( $checklist_values['min_qty'] ) ? intval( $checklist_values['min_qty'] ) : 6;
    ?>
    <div>Minimum Quantity</div>
    <input style='display: block' type='text' name='wc_custom_settings[min_qty]' value='<?php echo $min_qty;?>' />
  </div>


  <p></p>

</div>
