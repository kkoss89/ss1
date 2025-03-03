<?php
  $class_element = app_config('template')['form']['class_element'];
  $config_status = app_config('config')['status'];
  $current_config_status = (in_array($controller_name, $config_status)) ? $config_status[$controller_name] : $config_status['default'];
  $form_status = array_intersect_key(app_config('template')['status'], $current_config_status); 
  $form_status = array_combine(array_keys($form_status), array_column($form_status, 'name'));
  $form_account_types = array_combine(array_column($items_role_permission, 'id'), array_column($items_role_permission, 'name')); 
  $timezone_list = tz_list();
  $form_timezone = array_combine(array_column($timezone_list, 'zone'), array_column($timezone_list, 'time')); 
  $elements = [
    [
      'label'      => form_label('First name'),
      'element'    => form_input(['name' => 'first_name', 'value' => @$item['first_name'], 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Last name'),
      'element'    => form_input(['name' => 'last_name', 'value' => @$item['last_name'], 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Email'),
      'element'    => form_input(['name' => 'email', 'value' => @$item['email'], 'type' => 'email', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Password'),
      'element'    => form_input(['name' => 'password', 'value' => @$item['password'], 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
      'type'       => 'password',
    ],
    [
      'label'      => form_label('Role type'),
      'element'    => form_dropdown('role_id', $form_account_types, @$item['role_id'], ['class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Status'),
      'element'    => form_dropdown('status', $form_status, @$item['status'], ['class' => $class_element]),
      'class_main' => "col-md-6",
    ],
    [
      'label'      => form_label('Timezone'),
      'element'    => form_dropdown('timezone', $form_timezone, @$item['timezone'], ['class' => $class_element]),
      'class_main' => "col-md-6",
    ],
  ];

  if (!empty($item['ids'])) {
    $ids = $item['ids'];
    $modal_title = 'Edit (' . $item['email'] . ')';
    $elements = array_filter($elements, function($value) { 
      if (isset($value['type'])) {
        return $value['type'] !== 'password'; 
      }
      return $value;
    });
  } else {
    $ids = null;
    $modal_title = 'Add new Staff';
  }

  $form_url = admin_url($controller_name."/store/");
  $redirect_url = admin_url($controller_name) . '?' . http_build_query(['field' => 'email','query' => $item['email']]);
  $form_attributes = array('class' => 'form actionForm', 'data-redirect' => $redirect_url, 'method' => "POST");
  $form_hidden = ['ids' => @$item['ids']];
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
