<?php
  $payment_elements = [
    [
      'label'      => form_label('Webmoney Purse'),
      'element'    => form_input(['name' => "payment_params[option][purse]", 'value' => @$payment_option->purse, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Webmoney Secret'),
      'element'    => form_input(['name' => "payment_params[option][secret]", 'value' => @$payment_option->secret, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];
  echo render_elements_form($payment_elements);
?>

<div class="form-group">
  <div class="form-group">
    <label class="form-label">Config:</label>
    <ol>
      <li> To receive payments you must have minimum formal certificate with verified documents (or certificate of higher level). </li>
      <li>Go to <code class="text-primary">https://merchant.wmtransfer.com/conf/purses.asp</code></li>
      <li>Enter login details.</li>
      <li>Click change for USD purse or other purse
        <ul>
          <li>Test/Work modes: <strong class="text-danger">work</strong></li>
          <li>Merchant name: set your panel title</li>
          <li>Secret Key: set strong password</li>
          <li><strong class="text-danger">"Let to use URL sent in form"</strong> must be enabled</li>
          <li>Control sign forming method: <strong class="text-danger">SHA256</strong></li>
        </ul>
      </li>
    </ol>
  </div>
</div>