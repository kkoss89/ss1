<?php
  $form_url = admin_url($controller_name."/edit_item_ticket_message?action=edit&ids=" . $item['ids']);
  $redirect_url = admin_url($controller_name."/view/" . $item['ticket_id']);
  $modal_title = 'Edit Message';
  $form_attributes = array('class' => 'form actionForm', 'data-redirect' => $redirect_url, 'method' => "POST");
  $form_hidden = [
    'ids'   => @$item['ids'],
  ];
  $class_element = app_config('template')['form']['class_element'];
  
  $general_elements = [
    [
      'label'      => form_label('Message'),
      'element'    => form_textarea(['name' => 'message', 'value' => @$item['message'], 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];
?>
<div id="main-modal-content" class="payment-method-update-form">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-pantone">
          <h4 class="modal-title"><i class="fa fa-edit"></i> <?php echo $modal_title; ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <?php echo form_open($form_url, $form_attributes, $form_hidden); ?>
        <div class="modal-body">
          <div class="row justify-content-md-center">
            <?php echo render_elements_form($general_elements); ?>
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

