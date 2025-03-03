<?php
  $class_element = app_config('template')['form']['class_element'];
  $config_status = app_config('config')['status'];
  $current_config_status = (in_array($controller_name, $config_status)) ? $config_status[$controller_name] : $config_status['default'];
  $form_status = array_intersect_key(app_config('template')['status'], $current_config_status); 
  $form_status = array_combine(array_keys($form_status), array_column($form_status, 'name')); 

  $elements = [
    [
      'label'      => form_label('Name (Default English Version)'),
      'element'    => form_input(['name' => 'name', 'value' => @$item['name'], 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];
  
  $elements_name_version = [];
  if ($items_lang) {
    foreach ($items_lang as $key => $item_lang) {
      if ($key == 'en') {
        continue;
      }
      $item_label = 'Name ('. language_codes($item_lang['code']).' Version)';
      $item_lang_name = '';
      if (!empty($item)) {
        $lang_names = json_decode($item['lang_name'], true);
        $item_lang_name = (isset($lang_names[$key]) && $lang_names[$key] != '') ? $lang_names[$key] : $item['name'];
      }
      $elements_name = [
        'label'      => form_label($item_label),
        'element'    => form_input(['name' => "lang_name[$key]", 'value' => @$item_lang_name, 'type' => 'text', 'class' => $class_element]),
        'class_main' => "col-md-12 col-sm-12 col-xs-12",
      ];
      $elements_name_version[] = $elements_name;
    }
  }
  $elements = array_merge($elements, $elements_name_version, [[
    'label'      => form_label('Status'),
    'element'    => form_dropdown('status', $form_status, @$item['status'], ['class' => $class_element]),
    'class_main' => "col-md-12 col-sm-12 col-xs-12",
  ]]);
  $modal_title = 'Add New';
  if (!empty($item['id'])) {
    $ids = $item['id'];
    $modal_title = 'Edit Category';
  }
  $form_url     = admin_url($controller_name."/store/");
  $redirect_url = admin_url($controller_name);
  $form_attributes = ['class' => 'form actionForm', 'data-redirect' => $redirect_url, 'method' => "POST"];
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
            <div class="col-md-12">
              <div class="form-group">
                <label>URL Slug <span class="form-required">*</span></label>
                <div class="input-group">
                  <span class="input-group-prepend" id="basic-addon3">
                    <span class="input-group-text text-muted"><?php echo cn('blog/'); ?></span>
                  </span>
                  <input type="text" name="url_slug" class="form-control" value="<?=@$item['url_slug'];?>">
                </div>
              </div> 
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
  "use strict";
  // Convert to Url Slug
  $(document).on("keyup", "input[type=text][name=name]", function(){
    console.log('test');
    
    var _that  = $(this),
      _value = _that.val();
      _value = convertToSlug(_value);
    $("input[name=url_slug]").val(_value);  
  });

  function convertToSlug(Text) {
    return Text
        .toLowerCase()
        .replace(/ /g,'-')
        .replace(/[^\w-]+/g,'')
        ;
  }
</script>
