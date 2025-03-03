<?php 
  $payment_elements = [
    [
      'label'      => form_label('Merchant ID'),
      'element'    => form_input(['name' => "payment_params[option][merchant_id]", 'value' => @$payment_option->merchant_id, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Payment API key'),
      'element'    => form_input(['name' => "payment_params[option][payment_api_key]", 'value' => @$payment_option->payment_api_key, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];  
  echo render_elements_form($payment_elements);
?>

<div class="form-group">
  <label class="form-label">Config:</label>
  <div class="description-content">
    <ol class="small">
      <li>Go to <a href="https://doc.cryptomus.com/getting-started/getting-api-keys /" target="_blank">https://doc.cryptomus.com/getting-started/getting-api-keys</a> and follow the instruction</li>
      <li>Settings → API keys
        <ul>
          <li>Copy <strong>Merchant ID</strong> value and fill it below</li>
          <li>Copy <strong>Payment API Key</strong> value and fill it below</li>
        </ul>
      </li>
    </ol>
  </div>
</div>
