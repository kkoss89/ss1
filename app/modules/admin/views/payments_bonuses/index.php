<?php  
  // Page header
  echo show_page_header($controller_name, ['page-options' => 'add-new', 'page-options-type' => 'ajax-modal']);
  // Page header Filter
  echo show_page_header_filter($controller_name, ['items_status_count' => $items_status_count, 'params' => $params]);
?>
<style>
	.form-check-input:checked{
		background-color: #d83f3f !important;
	}
</style>
<div class="row">
  <?php if (!empty($items)) {
  ?>
    <div class="col-md-12">
      <div class="card">
        <!-- Card headers -->
        <?php $this->load->view('../partials_template/list_card_header.php'); ?>
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-vcenter card-table">
            <?php echo render_table_thead($columns); ?>
            <tbody>
              <?php if (!empty($items)) {
                $i = $from;
                foreach ($items as $key => $item) {
                  $i++;
                  $item_checkbox      = show_item_check_box('check_item', $item['id']);
                  $item_status        = show_item_status($controller_name, $item['id'], $item['status'], 'switch');
                  $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
              ?>
                <tr class="tr_<?php echo esc($item['id']); ?>">
                  <th class="text-center"><?php echo $item_checkbox; ?></th>
                  <td class="text-center text-muted w-5p"><?=$i?></td>
                  <td>
                    <div class="title"><?php echo show_high_light(esc($item['name']), $params['search'], 'name'); ?></div>
                  </td>
				  <td class="text-center w-5p"><?php echo esc($item['bonus_description']); ?></td>
				  <td class="text-center w-5p">
					<?php
						if($item['bonus_user_type'] == 0){
							echo "All Users";
						} else {
							echo "Selected Users";
						}
					?>
				  </td>
                  <td class="text-center w-5p"><?php echo esc($item['bonus_from']); ?></td>
                  <td class="text-center w-5p"><?php echo esc($item['percentage']); ?></td>
                  <td class="text-center w-5p"><?php echo $item_status; ?></td>
                  <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
                </tr>
              <?php }}?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php echo show_pagination($pagination); ?>
  <?php } else {
    echo show_empty_item();
  }?>
</div>
