<?php
  $form_payment_curreyncy_codes = [
    'LKR ' => "LKR - Sri Lankan Rupee",
  ];
  $payment_elements = [
    [
      'label'      => form_label('Environment'),
      'element'    => form_dropdown('payment_params[option][environment]', $form_environment, @$payment_option->environment, ['class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('App ID'),
      'element'    => form_input(['name' => "payment_params[option][app_id]", 'value' => @$payment_option->app_id, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('App Token'),
      'element'    => form_input(['name' => "payment_params[option][app_token]", 'value' => @$payment_option->app_token, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Hash Salt'),
      'element'    => form_input(['name' => "payment_params[option][hash_key]", 'value' => @$payment_option->hash_key, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Callback Token'),
      'element'    => form_input(['name' => "payment_params[option][callback_token]", 'value' => @$payment_option->callback_token, 'type' => 'text', 'class' => $class_element]),
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
      'item2'      => ['name' => 'LKR', 'value' => '330'],
    ],
  ];
  echo render_elements_form($payment_elements);
?>

<div class="form-group">
  <div class="form-group">
    <label class="form-label">Config:</label>
    <ol class="small">
      <li> App ID, App Token and Hash Salt you may find on <strong>Developer Configurations &rarr; IPG APPs</strong> </li>
      <li> Callback URL: <code><?php echo cn('onepay_ipn'); ?></code></li>
      <li> Callback Token: Need to generate high Callback token on Onepay App</li>
    </ol>
  </div>
</div>