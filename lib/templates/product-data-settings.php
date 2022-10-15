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
<div id="wc_custom_logo_discount" class="panel woocommerce_options_panel hidden">
  <?php //print_r( $discount_break_values );?>
  <?php foreach( $discount_breaks as $discount_key ):?>
  <p class='form-field'>
    <label><?php _e( "Discount for $discount_key" );?></label>
    <input type='number' class='short wc_input_price' step='0.01' name='wc_custom_discount_breaks[<?php echo $discount_key;?>]' value='<?php echo $discount_break_values[ $discount_key ];?>' />
  </p>
  <?php endforeach;?>
</div>
