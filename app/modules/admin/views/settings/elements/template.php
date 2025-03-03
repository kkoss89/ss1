<?php
    $form_url = admin_url($controller_name."/store/");
    $form_attributes = array('class' => 'form actionForm', 'data-redirect' => get_current_url(), 'method' => "POST");
    $form_header_colors = [
        'default'            => 'Default',
        'purple'             => 'Purple',
        'light-blue'         => 'Light Blue',
        'lawrencium'         => 'Lawrencium',
        'cool-sky'           => 'Cool Sky',
        'dark-ocean'         => 'Dark Ocean',
        'cosmic-fusion'      => 'Cosmic Fusion',
        'royal'              => 'Royal',
        'twitch'             => 'Twitch',
        'bluelagoo'          => 'Bluelagoo',
        'dimigo'             => 'Dimigo',
    ];
    $class_element = app_config('template')['form']['class_element'];
    $elements_layout = [
        [
            'label'      => form_label('Header Menu Skin and Button Colors'),
            'element'    => form_dropdown('default_header_skin', $form_header_colors, get_option('default_header_skin', 'default'), ['class' => $class_element]),
            'class_main' => "col-md-6",
        ],
    ];
?>
<div class="card content">
  <div class="card-header">
    <h3 class="card-title"><i class="fe fe-layout"></i> Template</h3>
  </div>
  <?php echo form_open($form_url, $form_attributes); ?>
    <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <h5 class="text-info"><i class="fe fe-link"></i> User Layout</h5>
            <div class="form-group">
              <div class="custom-switches-stacked">
                <label class="custom-switch">
                  <input type="radio" name="user_layout" class="custom-switch-input" value="vertical" <?=(get_option('user_layout', "horizontal") == 'vertical')? "checked" : ''?>>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Vertical</span>
                </label>
                <label class="custom-switch">
                  <input type="radio" name="user_layout" value="horizontal" class="custom-switch-input" <?=(get_option('user_layout', "horizontal") == 'horizontal')? "checked" : ''?>> 
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Horizontal</span>
                </label>
              </div>
            </div>  
            <div class="row smtp-configure <?=(get_option('user_layout', "") == 'horizontal')? "" : 'd-none'?>">
                <?php echo render_elements_form($elements_layout); ?>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <h5 class="text-info"><i class="fe fe-link"></i> Default Landing Page</h5>
              <select  name="default_home_page" class="form-control square">
              <?php
                  $current_theme = get_option('default_home_page', 'regular');
                  $themes_arr = get_name_folder_from_dir();
                  if (!$themes_arr) {
                      $themes_arr = ['regular', 'pergo', 'moka'];
                  }
                  foreach ($themes_arr as $key => $theme) {
              ?>
                  <option value="<?php echo $theme; ?>" <?=( strtolower($current_theme) == $theme) ? 'selected': ''?>> <?php echo ucfirst($theme); ?></option>
                  <?php } ?>
              </select>
            </div>
          </div>
        </div>
        <?php
            $is_nico_landing_page_type = false; 
            if (in_array(get_option('default_home_page', 'regular'), ['nico'])) {
              $is_nico_landing_page_type = true;  
            }
        ?>
        <h5 class="nico-landing-page <?=($is_nico_landing_page_type) ? '' : 'd-none'?>"><i class="fe fe-slack"></i> Nico Landing page Options</h5>
        <div class="row nico-landing-page <?=($is_nico_landing_page_type) ? '' : 'd-none'?> m-l-20">
            <div class="col-md-6">
              <div class="form-group">
                <h5 class="text-success"><i class="fe fe-server"></i> Type</h5>
                <select  name="default_nico_type" class="form-control square">
                  <option value="light" <?=( get_option('default_nico_type', 'light') == 'light') ? 'selected': ''?>> Light</option>
                  <option value="dark" <?=( get_option('default_nico_type', 'light') == 'dark') ? 'selected': ''?>> Dark</option>
                </select>
              </div>  
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <h5 class="text-success"><i class="fe fe-server"></i> Default Number</h5>
              </div> 
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Happy clients</label>
                <input class="form-control" name="default_happy_clients_number" value="<?php echo get_option('default_happy_clients_number', rand(5239, 23989)); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Total orders</label>
                <input class="form-control" name="default_total_orders_number" value="<?php echo get_option('default_total_orders_number', rand(132397, 239897)); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Hours of support</label>
                <input class="form-control" name="default_hours_of_support_number" value="<?php echo get_option('default_hours_of_support_number', rand(13239, 23989)); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Fast services</label>
                <input class="form-control" name="default_fast_services_number" value="<?php echo get_option('default_fast_services_number', rand(239, 5897)); ?>">
              </div>
            </div>
        </div>
        
    </div>
    <div class="card-footer text-end">
      <button class="btn btn-primary btn-min-width text-uppercase"><?=lang("Save")?></button>
    </div>
  <?php echo form_close(); ?>
</div>

<script>
  // Check post type
  $(document).on("change","input[type=radio][name=user_layout]", function(){
    var _that = $(this);
    var _type = _that.val();
    if(_type == 'horizontal'){
      $('.smtp-configure').removeClass('d-none');
    }else{
      $('.smtp-configure').addClass('d-none');
    }
  });
  $(document).on("change","select[name=default_home_page]", function(){
    var _that = $(this);
    var _type = _that.val();
    if(_type == 'nico') {
      $('.nico-landing-page').removeClass('d-none');
    } else {
      $('.nico-landing-page').addClass('d-none');
    }
  });
</script>