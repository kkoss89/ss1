<?php
  $form_payment_curreyncy_codes = [
    'USD' => "USD - US Dollar",
    'RUB' => "RUB",
    'EUR' => "EUR",
    'UAH' => "UAH",
    'KZT' => "KZT",
  ];

  $payment_elements = [
    [
      'label'      => form_label('Merchant ID'),
      'element'    => form_input(['name' => "payment_params[option][merchant_id]", 'value' => @$payment_option->merchant_id, 'type' => 'text', 'class' => $class_element]),
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

<style>
  .form-group-item .g-btn-add .btn-add-item{
    margin-top: 20px
  }
  .form-group-item .g-items .item {
    border: solid 1px #d1d1d1;
    border-top: 0;
    padding: 0 15px
  }

  .form-group-item .g-items .item .form-control {
    margin-bottom: 10px
  }

  .form-group-item .g-items .item .form-control:last-child {
    margin-bottom: 0
  }

  .form-group-item .g-items .item>.row>div {
    padding: 10px;
    border-right: 1px solid #d1d1d1
  }

  .form-group-item .g-items .item>.row>div:last-child {
    border-right: 0
  }

  .form-group-item .g-items .item textarea.full-h {
    height: 100%
  }

  .form-group-item .g-more {
    display: none
  }

  .form-group-item label {
    display: block;
    min-height: 20px
  }

  .form-group-item .g-items-header {
    font-weight: 700;
    border: solid 1px #d1d1d1;
    padding: 10px;
    text-align: center
  }
</style>
<!-- Product -->
<div class="col-md-12">
  <div class="form-group-item">
    <label class="control-label form-label">Payment Method Acceptance Settings:</label>
    <div class="g-items-header">
        <div class="row">
            <div class="col-md-5">ID</div>
            <div class="col-md-5">Name</div>
            <div class="col-md-1"></div>
        </div>
    </div>
    <div class="g-items">
      <?php
        $payments_method = @$payment_option->payments_method;
        if ($payments_method) {
          $i = 0;
          foreach ($payments_method as $key => $item) {
            $i++;
      ?>
        <div class="item" data-number="<?php echo $i; ?>">
          <div class="row">
            <div class="col-md-5">
              <input type="text" name="payment_params[option][payments_method][<?php echo $key; ?>][id]" class="form-control" value="<?= $item->id; ?>">
            </div>
            <div class="col-md-6">
              <input type="text" name="payment_params[option][payments_method][<?php echo $key; ?>][name]" class="form-control" value="<?= $item->name; ?>">
            </div>
            <div class="col-md-1 text-center">
                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
            </div>
          </div>
        </div>
      <?php }} ?>
    </div>
    <div class="text-right g-btn-add">
      <span class="btn btn-success btn-sm btn-add-item"><i class="fe fe-plus-circle"></i> Add item</span>
    </div>
    <div class="g-more d-none">
      <div class="item" data-number="__number__">
        <div class="row">
          <div class="col-md-5">
            <input type="text" __name__="payment_params[option][payments_method][__number__][id]" class="form-control" placeholder="Eg: 1">
          </div>
          <div class="col-md-6">
            <input type="text" __name__="payment_params[option][payments_method][__number__][name]" class="form-control" placeholder="Eg: FK WALLET RUB">
          </div>
          <div class="col-md-1 text-center">
            <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="form-group">
  <span class="text-danger"><strong><?=lang('note')?></strong></span>
  <ul class="small">
    <li> Go to the Free Kassa settings page </li>
    <li> Select the notification method <code class="text-primary">POST</code> </li>
    <li> Select the integration mode <code class="text-primary">NO</code></li>
    <li> Site URL: <code class="text-primary"><?=cn()?></code> </li>
    <li> Notification URL: <code class="text-primary"><?=cn('add_funds/freekassa/complete')?></code></li>
    <li> Success URL: <code class="text-primary"><?=cn('add_funds/freekassa/complete')?></code></li>
    <li> Unsuccess URL: <code class="text-primary"><?=cn('add_funds/unsuccess')?></code>
    <li> <Strong>Payment Option</Strong>:  Go to <a href="https://cutt.ly/X4gCGPa" target="_blank"><strong class="text-danger">this link</strong></a> to get Payment ID and name. <br>For example: if you enable Curency Code - USD, you must be choose all payment support USD and set rate in currency rate fields</li>
    </li>
  </ul>
</div>

<script>
  "use strict";
  $(document).ready(function() {
    // add new item
    $(".form-group-item .btn-add-item").click(function() {
      var number = $(this).closest(".form-group-item").find(".g-items .item:last-child").data("number");
      if (number === undefined) number = 0;
      else number++;
      var extra_html = $(this).closest(".form-group-item").find(".g-more").html();
      extra_html = extra_html.replace(/__name__=/gi, "name=");
      extra_html = extra_html.replace(/__number__/gi, number);
      $(this).closest(".form-group-item").find(".g-items").append(extra_html);
    });

    // Remove item
    $(".form-group-item").each(function() {
      var container = $(this);
      $(this).on('click', '.btn-remove-item', function() {
          $(this).closest(".item").remove();
      });
      $(this).on('press', 'input,select', function() {
        var value = $(this).val();
        $(this).attr("value", value);
      });
    });

  });
</script>
