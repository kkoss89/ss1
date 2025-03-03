<?php
    $class_element = app_config('template')['form']['class_element'];
    $class_element_checkbox = app_config('template')['form']['class_element_checkbox'];
    $form_sync_request = [
        '0' => 'Only for current services',
        '1' => 'All services'
    ];
    $auto_sync_settings = json_decode($auto_sync_settings, true);
    $elements = [
        [
            'label'      => form_label('Price percentage increase (%) (Auto rounding to 2 decimal places)'),
            'element'    => form_dropdown('price_percentage_increase', range(0, 500), @$auto_sync_settings['price_percentage_increase'], ['class' => $class_element]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
        ],
        [
            'label'      => form_label('Synchronous request'),
            'element'    => form_dropdown('sync_request', $form_sync_request, @$auto_sync_settings['sync_request_type'], ['class' => $class_element]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
        ],
        [
            'label'      => form_label('Sync New Price'),
            'element'    => form_checkbox(['name' => 'sync_request_options[new_price]', 'value' => 1,  'type' => 'checkbox', 'class' => $class_element_checkbox, 'checked' => @$auto_sync_settings['sync_request_options']['new_price']]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
            'type' => "checkbox",
        ],
        [
            'label'      => form_label('Sync Original Price'),
            'element'    => form_checkbox(['name' => 'sync_request_options[original_price]', 'value' => 1,  'type' => 'checkbox', 'class' => $class_element_checkbox, 'checked' => @$auto_sync_settings['sync_request_options']['original_price']]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
            'type' => "checkbox",
        ],
        [
            'label'      => form_label('Service Name'),
            'element'    => form_checkbox(['name' => 'sync_request_options[service_name]', 'value' => 1,  'type' => 'checkbox', 'class' => $class_element_checkbox, 'checked' => @$auto_sync_settings['sync_request_options']['service_name']]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
            'type' => "checkbox",
        ],
        [
            'label'      => form_label('Sync Service Status with provider'),
            'element'    => form_checkbox(['name' => 'sync_request_options[status_with_provider]', 'value' => 1,  'type' => 'checkbox', 'class' => $class_element_checkbox, 'checked' => @$auto_sync_settings['sync_request_options']['status_with_provider']]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
            'type' => "checkbox",
        ],
        [
            'label'      => form_label('Sync Min Order'),
            'element'    => form_checkbox(['name' => 'sync_request_options[min]', 'value' => 1,  'type' => 'checkbox', 'class' => $class_element_checkbox, 'checked' => @$auto_sync_settings['sync_request_options']['min']]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
            'type' => "checkbox",
        ],
        [
            'label'      => form_label('Sync Max Order'),
            'element'    => form_checkbox(['name' => 'sync_request_options[max]', 'value' => 1,  'type' => 'checkbox', 'class' => $class_element_checkbox, 'checked' => @$auto_sync_settings['sync_request_options']['max']]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
            'type' => "checkbox",
        ],
        [
            'label'      => form_label('Sync Dripfeed'),
            'element'    => form_checkbox(['name' => 'sync_request_options[dripfeed]', 'value' => 1,  'type' => 'checkbox', 'class' => $class_element_checkbox, 'checked' => @$auto_sync_settings['sync_request_options']['dripfeed']]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
            'type' => "checkbox",
        ],
        [
            'label'      => form_label('Sync Service Description (Support only HQ SmartPanel)'),
            'element'    => form_checkbox(['name' => 'sync_request_options[service_desc]', 'value' => 1,  'type' => 'checkbox', 'class' => $class_element_checkbox, 'checked' => @$auto_sync_settings['sync_request_options']['service_desc']]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
            'type' => "checkbox",
        ],
        [
            'label'      => form_label('Convert to new currency Rate (new currency Rate in Setting page)'),
            'element'    => form_checkbox(['name' => 'sync_request_options[convert_to_new_currency]', 'value' => 1,  'type' => 'checkbox', 'class' => $class_element_checkbox, 'checked' => @$auto_sync_settings['sync_request_options']['convert_to_new_currency']]),
            'class_main' => "col-md-12 col-sm-12 col-xs-12",
            'type' => "checkbox",
        ],
    ];

    if (is_table_exists(ORDERS_REFILL)) {
        $refill_element = [
            [
                'label'      => form_label('Sync Refill'),
                'element'    => form_checkbox(['name' => 'sync_request_options[refill]', 'value' => 1, 'type' => 'checkbox', 'class' => $class_element_checkbox, 'checked' => @$auto_sync_settings['sync_request_options']['refill']]),
                'class_main' => "col-md-12 col-sm-12 col-xs-12",
                'type' => "checkbox",
            ],
        ];
        $elements = array_merge($elements, $refill_element);
    }

    $modal_title = 'Auto Sync Services Settings';
    $form_url        = admin_url($controller_name."/auto_sync_setting/");
    $form_attributes = array('class' => 'form actionForm', 'method' => "POST");
    $form_hidden     = ['action'     => 'auto_sync_setting'];
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
          <div class="row justify-content-md-center">
            <?php echo render_elements_form($elements); ?>

            <div class="col-md-12">
                <span class="text-danger">Note:</span>
                <ul class="text-muted">
                    <li> Synchronous request:
                        <ol>
                            <li><strong class="text-success">Current Service</strong>: Syncing all current services available</li>
                            <li><strong class="text-success">All Services</strong>: Syncing all services available and add new service automatically if service doesn't exists</li>
                        </ol>
                    </li>
                </ul>
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
