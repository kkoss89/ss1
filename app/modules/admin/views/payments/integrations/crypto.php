<?php 
  $payment_elements = [
    [
      'label'      => form_label('Payment URL'),
      'element'    => form_input(['name' => "payment_params[option][payment_url]", 'value' => @$payment_option->payment_url, 'type' => 'url', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Checkout ID'),
      'element'    => form_input(['name' => "payment_params[option][checkout_id]", 'value' => @$payment_option->checkout_id, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];  
  echo render_elements_form($payment_elements);
?>
