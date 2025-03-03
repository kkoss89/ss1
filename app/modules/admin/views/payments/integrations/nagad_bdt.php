<?php
  $form_payment_curreyncy_codes = [
    'USD' => "USD - US Dollar",
    'BDT' => "BDT",
  ];
  $form_active_amount_tnx_id = [
    0 => "Not Allowed",
    1 => "Allowed",
  ];
  $payment_elements = [
    [
      'label'      => form_label('Display: the required amount, transaction id field on add funds form'),
      'element'    => form_dropdown('payment_params[option][active_amount_tnx_id_fields]', $form_active_amount_tnx_id, @$payment_option->active_amount_tnx_id_fields, ['class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('QR code image URL Link (optional)'),
      'element'    => form_input(['name' => "payment_params[option][qr_code]", 'value' => @$payment_option->qr_code, 'type' => 'text', 'class' => $class_element]),
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
      'item1'      => ['name' => get_option('currecy_code', 'USD'), 'value' => 1],
      'item2'      => ['name' => 'BDT', 'value' => 95],
    ],
  ];
  echo render_elements_form($payment_elements);
?>
