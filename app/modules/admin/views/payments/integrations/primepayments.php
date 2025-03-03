<?php
  $form_payment_curreyncy_codes = [
    'USD' => "USD - US Dollar",
    'RUB' => "RUB",
  ];

  $payment_elements = [
    [
      'label'      => form_label('Merchant ID'),
      'element'    => form_input(['name' => "payment_params[option][project_id]", 'value' => @$payment_option->project_id, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Secret Word'),
      'element'    => form_input(['name' => "payment_params[option][secret_key]", 'value' => @$payment_option->secret_key, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Secret Word 2'),
      'element'    => form_input(['name' => "payment_params[option][secret_key_2]", 'value' => @$payment_option->secret_key_2, 'type' => 'text', 'class' => $class_element]),
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
      'item1'      => ['name' => get_option('currecy_code', 'USD'), 'value' => 1],
      'item2'      => ['name' => @$payment_option->currency_code, 'value' => 168],
    ],
  ];

  echo render_elements_form($payment_elements);
?>

<div class="form-group">
  <span class="text-danger"><strong><?=lang('note')?></strong></span>
  <ul class="small">
    <li>Go to the project settings and setup the all URLs as: </li>
    <li>Status URL: <code class="text-primary"><?=cn('primepayments_ipn')?></code></li>
    <li>Success URL: <code class="text-primary"><?=cn('add_funds/primepayments/complete?ID={innerID}')?></code></li>
    <li>Unsuccess URL: <code class="text-primary"><?=cn('add_funds/unsuccess')?></code>
    <li>Fill out Project ID field with ID of your project in the payment method.</li>
    <li>Fill out API secret 1 and API secret 2 from your payment method settings.</li>
  </ul>
</div>
