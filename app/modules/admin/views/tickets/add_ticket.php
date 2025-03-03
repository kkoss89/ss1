<?php
  $class_element = app_config('template')['form']['class_element'];
  $elements = [
    [
      'label'      => form_label('Email'),
      'element'    => form_input(['name' => 'email', 'value' => '', 'placeholder' => 'Enter an email user', 'type' => 'email', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Subject'),
      'element'    => form_input(['name' => 'subject', 'value' => '', 'placeholder' => 'Enter a subject', 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label'      => form_label('Message'),
      'element'    => form_textarea(['name' => 'message', 'value' => '', 'class' => $class_element]),
      'class_main' => "col-md-12",
    ],
    
  ];

  $modal_title = 'Add ticket';

  $form_url = admin_url($controller_name."/add_ticket/");
  $redirect_url = admin_url($controller_name);
  $form_attributes = array('class' => 'form actionForm', 'data-redirect' => $redirect_url, 'method' => "POST");
  $form_hidden = [
    'action'   => 'add-ticket',
  ];
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
          <button type="submit" class="btn btn-primary btn-min-width mr-1 mb-1">Submit</button>
          <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
        </div>
        <?php echo form_close(); ?>
    </div>
  </div>
</div>
