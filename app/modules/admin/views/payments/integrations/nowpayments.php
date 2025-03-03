<?php 
  $payment_elements = [
    [
      'label'      => form_label('API KEY'),
      'element'    => form_input(['name' => "payment_params[option][api_key]", 'value' => @$payment_option->api_key, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('IPN Secret'),
      'element'    => form_input(['name' => "payment_params[option][ipn_secret]", 'value' => @$payment_option->ipn_secret, 'type' => 'password', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ]; 
  echo render_elements_form($payment_elements);
?>

<div class="form-group">
  <label class="form-label">Config:</label>
  <div class="description-content">
    <ol class="small">
      <li>Go to <a href="https://documenter.getpostman.com/view/7907941/2s93JusNJt#intro" target="_blank">Documentation</a> and follow the instruction</li>
      <li>Settings → API keys
        <ul>
          <li>Copy <strong>API KEY</strong> value and fill it below</li>
          <li>Copy <strong>Public API KEY</strong> value and fill it below</li>
        </ul>
      </li>
    </ol>
  </div>
</div>
