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
<?php
  $payment_elements = [
    [
      'label'      => form_label('Popup Storefronts'),
      'element'    => form_input(['name' => "payment_params[option][popup_storefronts]", 'value' => @$payment_option->popup_storefronts, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Username'),
      'element'    => form_input(['name' => "payment_params[option][username]", 'value' => @$payment_option->username, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Password'),
      'element'    => form_input(['name' => "payment_params[option][password]", 'value' => @$payment_option->password, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];
  echo render_elements_form($payment_elements);
?>

<!-- Product -->
<div class="col-md-12">
  <div class="form-group-item">
    <label class="control-label form-label">Products</label>
    <div class="g-items-header">
        <div class="row">
            <div class="col-md-5">Path</div>
            <div class="col-md-5">Pricing (USD)</div>
            <div class="col-md-1"></div>
        </div>
    </div>
    <div class="g-items">
      <?php
        $products = @$payment_option->products;
        if ($products) {
          $i = 0;
          foreach ($products as $key => $item) {
            $i++;
      ?>
        <div class="item" data-number="<?php echo $i; ?>">
          <div class="row">
            <div class="col-md-5">
              <input type="text" name="payment_params[option][products][<?php echo $key; ?>][path]" class="form-control" value="<?= $item->path; ?>">
            </div>
            <div class="col-md-6">
              <input type="text" name="payment_params[option][products][<?php echo $key; ?>][pricing]" class="form-control" value="<?= $item->pricing; ?>">
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
            <input type="text" __name__="payment_params[option][products][__number__][path]" class="form-control" placeholder="Eg: purchase-smartpanel-regular">
          </div>
          <div class="col-md-6">
            <input type="text" __name__="payment_params[option][products][__number__][pricing]" class="form-control" placeholder="Eg: 39">
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
  <div class="form-group">
    <label class="form-label">Config:</label>
    <ul class="small">
      <li> Find Username and Password on <strong>fastspring.com</strong>  API Credentials </li>
      <li> Create a <strong>Popup Storefronts</strong> ->  Copy the path of Popup Storefronts -> Paste to Popup Storefronts field
        <ol> Step by Step:
          <li>Navigate to <strong>Storefronts</strong> &gt; <strong>Popup Storefronts</strong>. Click <strong>Create Popup Storefront</strong>.</li>
          <li>Below <strong>Company Sub-Directory Storefront ID</strong>, enter an ID for the storefront. This becomes part of the storefront's URL.&nbsp;</li>
          <li>Click <strong>Create</strong>. Your new popup storefront appears in the Popup Storefronts page.&nbsp;</li>
          <li>More details: <a href="https://fastspring.com/docs/customize-your-popup-storefront" target="_blank" rel="noopener noreferrer">https://fastspring.com/docs/customize-your-popup-storefront</a></li>
        </ol>
      </li>
      <li> Create <strong>products</strong> (with hiden Quanlity feature) ->  Copy the path, pricing of product -> Paste to Products field</li>
    </ul>
  </div>
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

