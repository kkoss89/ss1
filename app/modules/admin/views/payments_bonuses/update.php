<?php 
	$class_element            = app_config('template')['form']['class_element'];
	$class_element_text_emoji = app_config('template')['form']['class_element_text_emoji'];
	$config_status            = app_config('config')['status'];
	$current_config_status    = (in_array($controller_name, $config_status)) ? $config_status[$controller_name] : $config_status['default'];
	$form_status              = array_intersect_key(app_config('template')['status'], $current_config_status); 
	$form_status              = array_combine(array_keys($form_status), array_column($form_status, 'name')); 
	
	$bonus_user_type_check    = ($item['bonus_user_type'] == 0) ? true : false;
	$bonus_user_type_check2   = ($item['bonus_user_type'] == 1) ? true : false;
	
	$users                    = array_combine(array_column($all_users, 'id'), array_column($all_users, 'name')); 
	$selected_users           = ($item['user_ids'] != '') ? explode(",",$item['user_ids']) : [];

  if ($items_payment) {
    $form_items_payment = array_combine(array_column($items_payment, 'id'), array_column($items_payment, 'name'));
  }else{
    $form_items_payment = ['0' => 'No Payment Option'];
  }
  $elements = [
    [
      'label'      => form_label('Payment Method'),
      'element'    => form_dropdown('payment_id', $form_items_payment, @$item['payment_id'], ['class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Bonus percentage (%)'),
      'element'    => form_input(['name' => 'percentage', 'value' => @$item['percentage'], 'type' => 'number', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Deposit from'),
      'element'    => form_input(['name' => 'bonus_from', 'value' => @$item['bonus_from'], 'type' => 'number', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
	[
      'label'      => form_label('Description'),
      'element'    => form_textarea(['name' => 'bonus_description','rows' => '3', 'value' => @$item['bonus_description'], 'class' => $class_element_text_emoji]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
	[
      'label'      => form_label('All Users','',['for' => 'all_bonus_user_type','class' => 'form-check-label']),
      'element'    => form_radio(['name' => "bonus_user_type", 'value' => 0, 'checked' => $bonus_user_type_check, 'class' => 'form-check-input', 'id' => 'all_bonus_user_type']),
	  'label_two'  => form_label('Selected Users','',['for' => 'selected_bonus_user_type','class' => 'form-check-label']),
      'element_two'=> form_radio(['name' => "bonus_user_type", 'value' => 1, 'checked' => $bonus_user_type_check2, 'class' => 'form-check-input', 'id' => 'selected_bonus_user_type']),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
	  'type'       => 'radio'
    ],
	[
      'label'      => form_label('Select Users'),
      'element'    => form_multiselect('user_ids[]', $users,$selected_users, ['class' => $class_element,'id' => 'user_ids']),
      'class_main' => "col-md-12 col-sm-12 col-xs-12 selectUserDiv",
    ],
    [
      'label'      => form_label('Status'),
      'element'    => form_dropdown('status', $form_status, @$item['status'], ['class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];
  if (!empty($item['id'])) {
    $modal_title = 'Edit';
  } else {
    $modal_title = 'Add new';
  }

  $form_url = admin_url($controller_name."/store/");
  $redirect_url = admin_url($controller_name);
  $form_attributes = array('class' => 'form actionForm', 'data-redirect' => $redirect_url, 'method' => "POST");
  $form_hidden = ['id' => @$item['id']];
?>
<div id="main-modal-content">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header bg-pantone">
          <h4 class="modal-title"><i class="fa fa-edit"></i><?php echo $modal_title; ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <?php echo form_open($form_url, $form_attributes, $form_hidden); ?>
        <div class="modal-body">
          <div class="row justify-content-md-center">
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
<script>
	$(document).ready(function(){
		<?php
			if($bonus_user_type_check2 == 1){
		?>
		$(".selectUserDiv").show();
		<?php
			} else {
		?>
		$(".selectUserDiv").hide();
		<?php		
			}
		?>
	});
	
	$('input:radio[name="bonus_user_type"]').change(function() {
		$("#user_ids").val(null);
        if ($(this).val() == '1') {
            $(".selectUserDiv").show();
        } else {
            $(".selectUserDiv").hide();
        }
    });
</script>