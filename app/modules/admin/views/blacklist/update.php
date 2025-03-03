<?php
    $class_element = app_config('template')['form']['class_element'];
    $config_status = app_config('config')['status'];
    $current_config_status = (in_array($controller_name, $config_status)) ? $config_status[$controller_name] : $config_status['default'];
    $form_status = array_intersect_key(app_config('template')['status'], $current_config_status); 
    $form_status = array_combine(array_keys($form_status), array_column($form_status, 'name')); 
    if ($sub_controller == 'ip') {
      $elements = [
        [
          'label' => form_label('IP address', 'IP address'),
          'element' => form_input(['name' => 'ip', 'value' => @$item['ip'], 'type' => 'text', 'required' => 'required', 'class' => $class_element]),
          'class_main' => "col-md-12 col-sm-12 col-xs-12",
        ],
      ];
    }
    if ($sub_controller == 'link') {
      $elements = [
        [
          'label' => form_label('Order link', 'link'),
          'element' => form_input(['name' => 'link', 'value' => @$item['link'], 'type' => 'text', 'required' => 'required', 'class' => $class_element]),
          'class_main' => "col-md-12 col-sm-12 col-xs-12",
        ],
      ];
    }
    if ($sub_controller == 'email') {
      $elements = [
        [
          'label' => form_label('Email', 'email'),
          'element' => form_input(['name' => 'email', 'value' => @$item['email'], 'type' => 'text', 'required' => 'required', 'class' => $class_element]),
          'class_main' => "col-md-12 col-sm-12 col-xs-12",
        ],
      ];
    }
    $elements = array_merge($elements, [
      [
        'label' => form_label('Status'),
        'element' => form_dropdown('status', $form_status, @$item['status'], ['class' => $class_element]),
        'class_main' => "col-md-12 col-sm-12 col-xs-12",
      ],
      [
        'label'      => form_label('Description'),
        'element'    => form_textarea(['name' => 'description', 'value' => @$item['description'], 'rows' => '3', 'class' => $class_element]),
        'class_main' => "col-md-12",
      ],
    ]);
    if (!empty($item['ids'])) {
      $ids = $item['ids'];
      $modal_title = 'Edit';
    } else {
      $ids = null;
      $modal_title = 'Add new';
    }
    $form_url = admin_url($controller_name . "/store/");
    $redirect_url = admin_url($controller_name) . "/" . $sub_controller;
    $form_attributes = array('class' => 'form actionForm', 'data-redirect' => $redirect_url, 'method' => "POST");
    $form_hidden = ['ids' => @$item['ids'], 'type' => get('type')];
?>
<div id="main-modal-content">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-pantone">
          <h4 class="modal-title"><i class="fa fa-edit"></i> <?php echo $modal_title; ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <?php echo form_open($form_url, $form_attributes, $form_hidden); ?>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <small>This feature will allow you to block a range of IP addresses, Email, Link to prevent them from accessing your site (register, login).</small>
                </div>
            </div>
            <?php echo render_elements_form($elements); ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-min-width mr-1 mb-1">Save</button>
          <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
        </div>
        <?php echo form_close(); ?>
    </div>
  </div>
</div>
