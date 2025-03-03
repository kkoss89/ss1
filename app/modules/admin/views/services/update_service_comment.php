<?php 
	$class_element            = app_config('template')['form']['class_element'];
	$class_element_text_emoji = app_config('template')['form']['class_element_text_emoji'];
	$config_status            = app_config('config')['status'];
	$current_config_status = (in_array($controller_name, $config_status)) ? $config_status[$controller_name] : $config_status['default'];
	$form_status = array_intersect_key(app_config('template')['status'], $current_config_status);
	$form_status = array_combine(array_keys($form_status), array_column($form_status, 'name'));

	$elements_item_description = [
		[
			'label' => form_label('Comment'),
			'element' => form_textarea(['name' => 'comment', 'value' => htmlspecialchars_decode(@$item['comment'], ENT_QUOTES), 'class' => $class_element_text_emoji]),
			'class_main' => "col-md-12",
		],
	];
	
	if (!empty($item['id'])) {
		$ids = $item['id'];
		$modal_title = 'Edit Comment (ID: ' . $item['id'] . ')';
	} else {
		$modal_title = 'Add new';
	}
	$form_url        = admin_url($controller_name . "/store_comments/");
	$redirect_url    = '';
	$form_attributes = array('class' => 'form actionForm', 'method' => "POST", 'data-redirect' => admin_url('services/comments/'.$service_id));
	
	$form_hidden = [
		'id'         => @$item['id'],
		'service_id' => @$service_id,
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
					
					<?php
						echo render_elements_form($elements_item_description);
					?>
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
