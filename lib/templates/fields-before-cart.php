<div class="wc-custom-logo-extras">
  <!--p>Click <a href='https://beepro.us/upload-logo/'>here</a> if you haven't uploaded your design</p-->
  <input type='hidden' name='wc_product_price' value='<?php _e( $product->get_price() );?>' />
  <input type='hidden' name='wc_min_qty' value='<?php _e( $allowed_settings['min_qty'] );?>' />
  <?php if( count( $allowed_label_designs ) ):?>
  <div style='margin:20px 0 40px;'>
    <p>Select one or more placements for your design:</p>
    <ul style='list-style:none; padding-left:0;'>
    <?php foreach( $allowed_label_designs as $slug => $label_design ):?>
      <li>
        <label>
          <input type='checkbox' name='wc_custom_label_design[]' value='<?php echo $slug;?>' />
          <span><?php _e( $label_design['label'] );?>&nbsp;<?php /*_e( '(+ $' .number_format( (float)$label_design['cost'], 2, '.', '') . ')' );*/ ?></span>
        </label>
      </li>
    <?php endforeach;?>
    </ul>
  </div>
  <?php endif;?>

  <div id='custom-text-container' style='margin-bottom: 30px;'>
    <label>Custom Text</label>
    <p><input type='text' name='wc_custom_text' value='' /></p>
  </div>

  <?php if( count( $allowed_sizes ) ):?>
  <div class='wc-table-container'>
    <table class='wc-table' data-behaviour='wc-custom-sizes'>
      <tr>
        <?php foreach( $allowed_sizes as $size ):?>
          <th><?php echo $size;?></th>
        <?php endforeach;?>
      </tr>
      <tr>
        <?php foreach( $allowed_sizes as $size ): $name = "wc_custom_sizes[$size]";?>
          <td><input type='number' name='<?php echo $name;?>' value='0' step='1' min='0' /></td>
        <?php endforeach;?>
      </tr>
    </table>
  </div>
  <?php endif;?>

  <script>
    window.label_designs = <?php echo json_encode( $label_designs );?>;
    window.discounts = <?php echo json_encode( wc_get_discounts_table() )?>;
    window.sizes_costs = <?php echo json_encode( getWCSizesCosts() )?>;
  </script>

  <input type='hidden' name='wc_custom_logo_image_src' value='<?php echo WC()->session->get( 'wc_custom_logo' );?>' />

  <div id='product_total_price' style='margin-bottom:20px;' data-currency='<?php echo $currency_symbol;?>'>
    <div>
      <p class='no-margin-bottom font-big'><span class='regular_price'></span>&nbsp;<span class='sale_price purple'></span> <span class='purple'>each</span></p>
      <p class='no-margin-bottom'><span class='estimated_price'></span>&nbsp;<span class='total_price purple'></span> <span class='purple'>total</span> with <span class='discount'>0%</span> Discount</p>
      <ul>
        <li>
          <i class='tshirt-multiple'></i>
          <span class="qty">1</span> Total Items
        </li>
        <li>
          <i class='tshirt-print'></i>
          <span class="label_designs_no">0</span> Print Area</li>
      </ul>
    </div>

    <div class='buy-more'>
      <h5>BUY MORE, SAVE MORE</h5>
      <p class='discount-text'></p>
      <p class='no-margin-bottom'><a href='' target='_blank' style='text-decoration: underline;'>Money Saving Tips</a></p>
    </div>

  </div>

  <div id='min_qty_text'>
    Minimum quantity for this product is <?php echo $allowed_settings['min_qty'];?>
  </div>
</div>
