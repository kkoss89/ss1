<?php
  $class_element = app_config('template')['form']['class_element'];
  $config_status = app_config('config')['status'];
  $current_config_status = (in_array($controller_name, $config_status)) ? $config_status[$controller_name] : $config_status['default'];
  $form_status = array_intersect_key(app_config('template')['status'], $current_config_status);
  $form_status = array_combine(array_keys($form_status), array_column($form_status, 'name'));

  $elements = [
      [
        'label' => form_label('Name'),
        'element' => form_input(['name' => 'name', 'value' => @$item['name'], 'type' => 'text', 'class' => $class_element]),
        'class_main' => "col-md-12 col-sm-12 col-xs-12",
      ],
      [
        'label' => form_label('Description'),
        'element' => form_textarea(['name' => 'description', 'value' => @$item['description'], 'rows' => 3, 'class' => $class_element]),
        'class_main' => "col-md-12 col-sm-12 col-xs-12",
      ],
  ];
  $modal_title = 'Add New';
  $permissions = [];
  if (!empty($item['id'])) {
    $ids = $item['id'];
    $modal_title = 'Edit Permission';
    $permissions = json_decode($item['permissions'], true);
  }
  $form_url = admin_url($controller_name . "/store/");
  $redirect_url = '';
  $form_attributes = ['class' => 'form actionForm', 'data-redirect' => $redirect_url, 'method' => "POST"];
  $form_hidden = ['id' => @$item['id']];
?>
<div id="main-modal-content">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-pantone">
          <h4 class="modal-title"><i class="fa fa-edit"></i><?php echo $modal_title; ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <?php echo form_open($form_url, $form_attributes, $form_hidden); ?>
        <div class="modal-body">
          <div class="row justify-content-md-center">
            <?php echo render_elements_form($elements); ?>
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="list__label m-b-10">Permission Access</div>
              <?php echo render_role_permission_form($permissions); ?>
            </div>
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

<script>
  // Check post type
  $(document).on("click",".access-controller", function() {
    var element = $(this);
    var access_controller_name = element.attr('data-class');
    var access_rule_list_area = $('.access-rule-' + access_controller_name);
    console.log(access_controller_name);
    if (element.is(":checked")) {
      access_rule_list_area.removeClass('d-none');
    } else {
      access_rule_list_area.addClass('d-none');
    }
  });
</script>