<div id="misha_product_data" class="panel woocommerce_options_panel hidden">
  <p class="form_field">Select to enable the following options:</p>
  <?php foreach( $options as $slug => $option ):?>
  <p class='form_field' style='margin:0; line-height:0.75;'>
    <input type='checkbox' name='wc_custom_settings[]' <?php if( in_array( $slug, $checklist_values ) ) _e( "checked='checked'" );?> value='<?php echo $slug;?>' />
    <?php echo $option;?>
  </p>
  <?php endforeach;?>
  <p></p>

  <!--div style="padding: 0 10px;">
    <h4>Additional price for each size</h4>
    <table class='wc-table'>
      <tr>
        <?php foreach( $sizes as $size ):?>
          <th><?php echo $size;?></th>
        <?php endforeach;?>
      </tr>
      <tr>
        <?php foreach( $sizes as $size ): $name = "wc_custom_sizes[ $size ]";?>
          <td><input type='number' name='<?php echo $name;?>' value='0' step='1' min='0' /></td>
        <?php endforeach;?>
      </tr>
    </table>
  </div-->
  <p></p>

</div>
