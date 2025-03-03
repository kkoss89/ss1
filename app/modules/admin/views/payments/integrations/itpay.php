<?php
  $payment_elements = [
    [
      'label'      => form_label('Shop ID'),
      'element'    => form_input(['name' => "payment_params[option][shop_id]", 'value' => @$payment_option->shop_id, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('API key'),
      'element'    => form_input(['name' => "payment_params[option][api_key]", 'value' => @$payment_option->api_key, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];
  echo render_elements_form($payment_elements);
?>

<div class="form-group">
  <span class="text-danger"><strong><?=lang('note')?></strong></span>
  <ul>
    <li>Login to: <a href="https://itpay.app/en/merchant/login" target="_blank">https://itpay.app/</a>, and setup all links:</li>
    <li><strong>Store URL: </strong> <code><?php echo cn(); ?></code></li>
    <li><strong>Success URL: </strong> <code><?php echo cn('add_funds/itpay/complete'); ?></code></li>
    <li><strong>Fail URL: </strong> <code><?php echo cn('add_funds/unsuccess'); ?></code></li>
    <li><strong>Result URL: </strong> <code><?php echo cn('itpay_ipn'); ?></code></li>
  </ul>
</div>