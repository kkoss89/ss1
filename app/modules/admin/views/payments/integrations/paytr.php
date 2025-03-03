<?php
  $form_payment_curreyncy_codes = [
    'USD' => "USD",
    'TL'  => "TL - Turkish lira",
  ];
  $payment_elements = [
    [
      'label'      => form_label('Merchant id'),
      'element'    => form_input(['name' => "payment_params[option][merchant_id]", 'value' => @$payment_option->merchant_id, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Merchant key'),
      'element'    => form_input(['name' => "payment_params[option][merchant_key]", 'value' => @$payment_option->merchant_key, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Merchant salt'),
      'element'    => form_input(['name' => "payment_params[option][merchant_salt]", 'value' => @$payment_option->merchant_salt, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Instructions'),
      'element'    => form_textarea(['name' => 'payment_params[option][instruction]', 'value' => @$payment_option->instruction, 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Currency code'),
      'element'    => form_dropdown('payment_params[option][currency_code]', $form_payment_curreyncy_codes, @$payment_option->currency_code, ['class' => $class_element . ' ajaxChangeCurrencyCode']),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Currency rate'),
      'element'    => form_input(['name' => "payment_params[option][rate_to_usd]", 'value' => @$payment_option->rate_to_usd, 'type' => 'text', 'class' => $class_element . ' text-right']),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
      'type'       => "exchange_option",
      'item1'      => ['name' => get_option('currency_code', 'USD'), 'value' => 1],
      'item2'      => ['name' => @$payment_option->currency_code, 'value' => 19],
    ],
  ];
  echo render_elements_form($payment_elements);
?>

<div class="form-group">
  <div class="form-group">
    <label class="form-label">Config:</label>
    <ul class="small">
      <li> Login to Paytr Account </li>
      <li> Go to Merchant Settings </li>
      <li> Notification URL Setting (Callback URL): <code><?php echo cn('paytr_ipn'); ?></code></li>
    </ul>
  </div>
</div>