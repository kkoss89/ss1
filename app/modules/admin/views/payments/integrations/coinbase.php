<?php
  $payment_elements = [
    [
      'label'      => form_label('API key'),
      'element'    => form_input(['name' => "payment_params[option][api_key]", 'value' => @$payment_option->api_key, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Webhook key'),
      'element'    => form_input(['name' => "payment_params[option][webhook_key]", 'value' => @$payment_option->webhook_key, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];
  echo render_elements_form($payment_elements);
?>

<div class="form-group">
  <label class="form-label">Config:</label>
  <div class="description-content">
    <ol class="small">
      <li>Sign in or create an <a href="https://commerce.coinbase.com/" target="_blank">account</a></li>
      <li>Settings → API keys
        <ul>
          <li>Generate API key by press <strong>Create an API key</strong> button</li>
          <li>Copy generated <strong>API key</strong> and paste here </li>
        </ul>
      </li>
      <li> Cron URL: <small><code  class="text-primary">* * * * * wget --spider -O - <?php echo cn('coinbase/cron'); ?> &gt;/dev/null 2&gt;&amp;1 </code></small></li>
    </ol>
  </div>
</div>
