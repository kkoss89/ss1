<?php
  $form_payment_curreyncy_codes = [
    'USD' => "USD - US Dollar",
    'LBP' => "LBP - Liban",
  ];
  $payment_elements = [
    [
      'label'      => form_label('Environment'),
      'element'    => form_dropdown('payment_params[option][environment]', $form_environment, @$payment_option->environment, ['class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Channel'),
      'element'    => form_input(['name' => "payment_params[option][api_channel]", 'value' => @$payment_option->api_channel, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Secret key'),
      'element'    => form_input(['name' => "payment_params[option][secret_key]", 'value' => @$payment_option->secret_key, 'type' => 'text', 'class' => $class_element]),
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
      'item2'      => ['name' => @$payment_option->currency_code, 'value' => '15200'],
    ],
  ];
  echo render_elements_form($payment_elements);
?>

<div class="form-group">
  <div class="form-group">
    <label class="form-label">Config:</label>
    <ol class="small">
      <li> Public key and Private key you may find on <strong>whishmoney.com/settings</strong> > API keys </li>
      <li> Go to <strong>youcanpay.com/settings/webhooks</strong> and click on Add webhook </li>
      <li> Webhook URL: <code><?php echo cn('whish_money_ipn'); ?></code></li>
      <li> Click on <b>Select event field</b> and choose all events.</li>
      <li> Click on <b>Select event field</b> and choose all events.</li>
    </ol>
  </div>
</div>