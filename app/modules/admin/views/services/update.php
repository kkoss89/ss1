<?php 
  $class_element = app_config('template')['form']['class_element'];
  $class_element_text_emoji = app_config('template')['form']['class_element_text_emoji'];
  $config_status = app_config('config')['status'];
  $current_config_status = (in_array($controller_name, $config_status)) ? $config_status[$controller_name] : $config_status['default'];
  $form_status = array_intersect_key(app_config('template')['status'], $current_config_status);
  $form_status = array_combine(array_keys($form_status), array_column($form_status, 'name'));

  $form_item_category = array_column($items_category, 'name', 'id');

  $form_service_mode = [
    'manual' => 'Manual',
    'api' => 'API',
  ];
  $elements_header = [
    [
      'label' => form_label('Service name'),
      'element' => form_input(['name' => 'name', 'value' => @$item['name'], 'type' => 'text', 'class' => $class_element, 'data-emojiable' => 'true']),
      'class_main' => "col-md-12 col-sm-12 col-xs-12 emoji-picker-container",
    ],
    [
      'label' => form_label('Category'),
      'element' => form_dropdown('category', $form_item_category, @$item['cate_id'], ['class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label' => form_label('Mode'),
      'element' => form_dropdown('add_type', $form_service_mode, @$item['add_type'], ['class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];

  $form_service_type = app_config('template')['service_type'];

  $form_dripfeed = $form_status;
  ksort($form_dripfeed);
  $elements_manual_mode = [
    [
      'label' => form_label('Service Type'),
      'element' => form_dropdown('service_type', $form_service_type, @$item['type'], ['class' => $class_element,'id' => 'manual_service_type_dropdown']),
      'class_main' => "",
    ],
    [
      'label' => form_label('Dripdfeed'),
      'element' => form_dropdown('dripfeed', $form_dripfeed, @$item['dripfeed'], ['class' => $class_element]),
      'class_main' => "",
    ],
  ];
  array_unshift($items_provider, ['id' => 0, 'name' => 'Choose Provider']);
  $form_providers = array_column($items_provider, 'name', 'id');

  $items_provider_service = [];
  array_unshift($items_provider_service, ['id' => 0, 'name' => 'Choose Service']);
  $items_provider_service = array_column($items_provider_service, 'name', 'id');
  $elements_api_mode = [
    [
      'label' => form_label('Provider'),
      'element' => form_dropdown('api_provider_id', $form_providers, @$item['api_provider_id'], ['class' => 'ajaxGetServicesFromAPI ' . $class_element]),
      'class_main' => "",
    ],
    [
      'label' => form_label('Service'),
      'element' => form_dropdown('api_service_id', $items_provider_service, @$item['api_service_id'], ['class' => $class_element . ' ajaxGetServiceDetail select-service-item', 'id' => 'select-service-item']),
      'class_main' => "form-group provider-services-list",
      'type' => "admin-change-provider-service-list",
    ],
    [
      'label' => form_label('Original Rate per 1000'),
      'element' => form_input(['name' => 'original_price', 'value' => @$item['original_price'], 'type' => 'text', 'readonly' => 'readonly', 'class' => $class_element]),
      'class_main' => "",
    ],
  ];

  $form_min              = (isset($item['min'])) ? $item['min'] : get_option('default_min_order', "");
  $form_max              = (isset($item['max'])) ? $item['max'] : get_option('default_max_order', "");
  $form_price            = (isset($item['price'])) ? $item['price'] : get_option('default_price_per_1k', "");
  $qty_percentage        = (isset($item['qty_percentage'])) ? $item['qty_percentage'] : 0;
  $is_regex_validation   = (isset($item['is_regex_validation'])) ? $item['is_regex_validation'] : 0;
  $regex_validations     = (isset($item['regex_validations'])) ? json_decode($item['regex_validations'],true) : [];
  $is_repeat_interval    = (isset($item['is_repeat_interval'])) ? $item['is_repeat_interval'] : 0;
  $runs                  = (isset($item['runs'])) ? $item['runs'] : 0;
  $interval              = (isset($item['interval'])) ? $item['interval'] : 0;
  $url_type              = (isset($item['url_type'])) ? $item['url_type'] : 0;
  $comments_enabled      = (isset($item['comments_enabled'])) ? $item['comments_enabled'] : 0;
  $previous_service_type = (isset($item['previous_service_type'])) ? $item['previous_service_type'] : '';
  
  $elements_item_detail = [
    [
      'label' => form_label('Min order'),
      'element' => form_input(['name' => 'min', 'value' => $form_min, 'type' => 'number', 'class' => $class_element]),
      'class_main' => "col-md-4 col-sm-12 col-xs-12",
    ],
    [
      'label' => form_label('Max order'),
      'element' => form_input(['name' => 'max', 'value' => $form_max, 'type' => 'number', 'class' => $class_element]),
      'class_main' => "col-md-4 col-sm-12 col-xs-12",
    ],
    [
      'label' => form_label('Rate per 1000'),
      'element' => form_input(['name' => 'price', 'value' => (double) $form_price, 'type' => 'text', 'class' => $class_element]),
      'class_main' => "col-md-4 col-sm-12 col-xs-12",
    ],
  ];

  $element_item_percentage = [
	[
      'label' => form_label('Quantity Percentage'),
      'element' => form_input(['name' => 'qty_percentage', 'value' => (double) $qty_percentage, 'type' => 'number', 'class' => $class_element, 'maxlength' => 3]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ]
  ];	

  $elements_item_description = [
    [
      'label' => form_label('Description'),
      'element' => form_textarea(['name' => 'desc', 'value' => htmlspecialchars_decode(@$item['desc'], ENT_QUOTES), 'class' => $class_element_text_emoji]),
      'class_main' => "col-md-12",
    ],
  ];
	
	$element_item_interval = [
		[
		  'label' => form_label('Total Runs'),
		  'element' => form_input(['name' => 'runs', 'value' => $runs, 'type' => 'number', 'class' => $class_element, 'id' => 'runs']),
		  'class_main' => "col-md-6 col-sm-6 col-xs-6",
		],
		[
		  'label' => form_label('Interval (Minutes)'),
		  'element' => form_input(['name' => 'interval', 'value' => $interval, 'type' => 'number', 'class' => $class_element, 'id' => 'interval']),
		  'class_main' => "col-md-6 col-sm-6 col-xs-6",
		]
	];
	
  if (!empty($item['id'])) {
    $ids = $item['id'];
    $modal_title = 'Edit Service (ID: ' . $item['id'] . ')';
  } else {
    $modal_title = 'Add new';
  }
  $form_url = admin_url($controller_name . "/store/");
  $redirect_url = '';
  $form_attributes = array('class' => 'form actionForm', 'method' => "POST");
  $form_hidden = [
    'id' => @$item['id'],
    'api_service_id' => @$item['api_service_id'],
    'api_service_type' => @$item['type'],
    'api_service_dripfeed' => @$item['dripfeed'],
    'api_service_refill' => @$item['refill'],
  ];
?>
<style>
  .form-control.select-service-item {
    padding: 0px;
  }
  .form-control.select-service-item .selectize-input{
    font-size: 14px;
    border-radius: 6px;
    margin-bottom: -6px;
  }
</style>

<div id="main-modal-content" class="crud-service-form">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-pantone">
          <h4 class="modal-title"><i class="fa fa-edit"></i> <?php echo $modal_title; ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <?php echo form_open($form_url, $form_attributes, $form_hidden); ?>
        <div class="modal-body">
          <div class="row justify-content-md-center">
            <div class="col-md-12 " id="alert_notification">
              
            </div>
            <?php echo render_elements_form($elements_header); ?>
            <div class="col-md-12">
              <?php
                if (isset($item['add_type']) && $item['add_type'] == 'api') {
                    $class_api_fieldset = '';
                    $class_manual_fieldset = 'd-none';
                } else {
                    $class_api_fieldset = 'd-none';
                    $class_manual_fieldset = '';
                }
              ?>
              <?php
                echo form_fieldset('', ['class' => 'form-fieldset api-mode ' . $class_api_fieldset]);
                echo render_elements_form($elements_api_mode);
                echo form_fieldset_close();

                echo form_fieldset('', ['class' => 'form-fieldset manual-mode ' . $class_manual_fieldset]);
                echo render_elements_form($elements_manual_mode);
                echo form_fieldset_close();
              ?>
            </div>
            <?php
              echo render_elements_form($elements_item_detail);
			  echo render_elements_form($element_item_percentage);
              $this->load->view('refill_option', ['item' => $item]);
              echo render_elements_form($elements_item_description);
            ?>
			
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label class="form-label">URL Type</label>
					<div class="custom-controls-stacked">
						<div class="row">
							<div class="col-4">
								<label class="form-check-inline custom-control-inline">Other URL Validation
									<input class="selectgroup-input" type="radio" name="url_type" value="0" <?php if($url_type == 0){ echo 'checked'; } ?>>
									<span class="checkmark"></span>
								</label>
							</div>
							<div class="col-4">
								<label class="form-check-inline custom-control-inline">Normalize URL Validation
									<input class="selectgroup-input" type="radio" name="url_type" value="1" <?php if($url_type == 1){ echo 'checked'; } ?>>
									<span class="checkmark"></span>
								</label>
							</div>
							<div class="col-4">
								<label class="form-check-inline custom-control-inline">No Validation (Live Stream)
									<input class="selectgroup-input" type="radio" name="url_type" value="2" <?php if($url_type == 2){ echo 'checked'; } ?>>
									<span class="checkmark"></span>
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label class="form-label">Is Regex Validation Require?</label>
					<div class="custom-controls-stacked">
						<div class="row">
							<div class="col-2">
								<label class="form-check-inline custom-control-inline">Yes
									<input class="selectgroup-input" type="radio" name="is_regex_validation" value="1" <?php if($is_regex_validation == 1){ echo 'checked'; } ?>>
									<span class="checkmark"></span>
								</label>
							</div>
							<div class="col-2">
								<label class="form-check-inline custom-control-inline">No
									<input class="selectgroup-input" type="radio" name="is_regex_validation" value="0" <?php if($is_regex_validation == 0){ echo 'checked'; } ?>>
									<span class="checkmark"></span>
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label class="form-label">Enable Comments Module</label>
					<div class="custom-controls-stacked">
						<input type="hidden" name="previous_service_type" id="previous_service_type" value="<?php echo $previous_service_type; ?>">
						<div class="row">
							<div class="col-2">
								<label class="form-check-inline custom-control-inline">Yes
									<input class="selectgroup-input" type="radio" name="comments_enabled" value="1" <?php if($comments_enabled == 1){ echo 'checked'; } ?>>
									<span class="checkmark"></span>
								</label>
							</div>
							<div class="col-2">
								<label class="form-check-inline custom-control-inline">No
									<input class="selectgroup-input" type="radio" name="comments_enabled" value="0" <?php if($comments_enabled == 0){ echo 'checked'; } ?>>
									<span class="checkmark"></span>
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-12 col-sm-12 col-xs-12" id="regexValidationStrings" <?php if($is_regex_validation == 0){ ?>style="display:none;" <?php } ?>>
				<div class="form-group" id="appendRegexValidationInputs">
					<?php
						if(!empty($regex_validations)){
							foreach($regex_validations as $key => $regex_validation){
								if($key == 0){
									?>
									<div class="row parentRegexStart mt-3">
										<div class="col-md-11">
											<label class="form-label">Regex String</label>
											<input type="text" name="regex_validations[]" class="form-control regex_validations_string" placeholder="Regex validation string" value="<?php echo $regex_validation; ?>">
										</div>
										<div class="col-md-1">
											<label class="form-label">&nbsp;</label>
										</div>
									</div>
									<?php
								} else {
									?>
									<div class="row parentRegexStart mt-3">
										<div class="col-md-11">
											<label class="form-label">Regex String</label>
											<input type="text" name="regex_validations[]" class="form-control regex_validations_string" placeholder="Regex validation string" value="<?php echo $regex_validation; ?>">
										</div>
										<div class="col-md-1">
											<label class="form-label">&nbsp;</label>
											<button type="button" class="btn btn-danger deleteThisRegex"><i class="fa fa-trash"></i></button>
										</div>
									</div>
									<?php
								}
							}
						} else {
					?>
					<div class="row">
						<div class="col-md-11">
							<label class="form-label">Regex String</label>
							<input type="text" name="regex_validations[]" class="form-control regex_validations_string" placeholder="Regex validation string">
						</div>
						<div class="col-md-1">
							<label class="form-label">&nbsp;</label>
						</div>
					</div>
					<?php
						}					
					?>
				</div>
				<div class="form-group">
					<div class="col-md-6">
						<button type="button" class="btn btn-primary" id="addMoreRegexString"><i class="fa fa-plus"></i> Add more regex string</button>
					</div>
				</div>
			</div>
			
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label class="form-label">Is Repeat Interval?</label>
					<div class="custom-controls-stacked">
						<div class="row">
							<div class="col-2">
								<label class="form-check-inline custom-control-inline">Yes
									<input class="selectgroup-input" type="radio" name="is_repeat_interval" value="1" <?php if($is_repeat_interval == 1){ echo 'checked'; } ?>>
									<span class="checkmark"></span>
								</label>
							</div>
							<div class="col-2">
								<label class="form-check-inline custom-control-inline">No
									<input class="selectgroup-input" type="radio" name="is_repeat_interval" value="0" <?php if($is_repeat_interval == 0){ echo 'checked'; } ?>>
									<span class="checkmark"></span>
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="row" id="intervalSettings" <?php if($is_repeat_interval == 0){ ?>style="display:none;" <?php } ?>>
					<?php echo render_elements_form($element_item_interval); ?>
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
  $(function() {
    window.emojiPicker = new EmojiPicker({
      emojiable_selector: '[data-emojiable=true]',
      assetsPath: "<?=BASE?>assets/plugins/emoji-picker/lib/img/",
      popupButtonClasses: 'fa fa-smile-o'
    });
    window.emojiPicker.discover();
  });

  $(document).ready(function() {
    $(".text-emoji").emojioneArea({
      pickerPosition: "top",
      tonesStyle: "bullet"
    });
  });
</script>

<script>
  var _token  = '<?php echo strip_tags($this->security->get_csrf_hash()); ?>';
  var pathGetProviderServicesURL  = '<?php echo admin_url($controller_name . '/provider_services/'); ?>';
  /*----------  Load default service with API  ----------*/
  $( document ).ready(function() {
    if ($('select[name=add_type]').val() == "api") {
      $('.provider-services-list').removeClass('d-none');
      $('.provider-services-list .dimmer').addClass('active');
      var id = $('select[name=api_provider_id]').val();
      if (id == "" || id == 0) return;
      var _api_service_id = $('input[name=api_service_id]').val();
      var data        = $.param({token:_token, provider_id:id, provider_service_id:_api_service_id});
      $.post(pathGetProviderServicesURL, data, function(_result) {
        setTimeout(function () {
          $('.provider-services-list .dimmer').removeClass('active');
          $(".provider-services-list select").html(_result);
          var _that = $( ".ajaxGetServiceDetail option:selected"),
              _rate = _that.attr("data-rate");
          var _refill = _that.attr("data-refill");
          if (_refill == 0) {
            $(".refill-type-option option[value='1']").remove();
          }
          $(".crud-service-form input[name=original_price]").val(_rate);
          if (!$('.select-service-item').hasClass('selectize-control')) {
            $('.select-service-item').selectize();
          }
        }, 100);
      });
      return false;
    }
  });
  
	$(document).on('change','input[name=is_regex_validation]',function(){
		if($(this).val() == 1){
			$("#regexValidationStrings").show();
			$(".regex_validations_string").attr('required',true);
		} else {
			$("#regexValidationStrings").hide();
			$(".regex_validations_string").attr('required',false);
		}
	});
  
	$(document).off('click', '#addMoreRegexString').on('click','#addMoreRegexString',function(event){
		
		event.preventDefault();
		
		var html = '';
		html += '<div class="row parentRegexStart mt-3">';
			html += '<div class="col-md-11">';
				html += '<label class="form-label">Regex String</label>';
				html += '<input type="text" name="regex_validations[]" class="form-control regex_validations_string" placeholder="Regex validation string" required>';
			html += '</div>';
			html += '<div class="col-md-1">';
				html += '<label class="form-label">&nbsp;</label>';
				html += '<button type="button" class="btn btn-danger deleteThisRegex"><i class="fa fa-trash"></i></button>';
			html += '</div>';
		html += '</div>';
		
		$("#appendRegexValidationInputs").append(html);
	});
  
	$(document).on('click','.deleteThisRegex',function(){
		$(this).closest('.parentRegexStart').remove(); 
	});
	
	$(document).on('change','input[name=is_repeat_interval]',function(){
		if($(this).val() == 1){
			$("#intervalSettings").show();
			$("#runs").attr('required',true);
			$("#interval").attr('required',true);
		} else {
			$("#intervalSettings").hide();
			$("#runs").attr('required',false);
			$("#interval").attr('required',false);
			$("#runs").val(0);
			$("#interval").attr(0);
		}
	});
	
	$(document).on('change','#manual_service_type_dropdown',function(){
		$(".crud-service-form input[name=previous_service_type]").val($(this).val());
	});
</script>
