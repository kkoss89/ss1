<?php 
  // Page header
  echo show_page_header($controller_name, ['page-options' => 'add-new', 'page-options-type' => 'ajax-modal']);
  // Page header Filter
  echo show_page_header_filter($controller_name, ['items_status_count' => $items_status_count, 'params' => $params]);
  
?>

<div class="row">
  <?php if(!empty($items)){
  ?>
    <div class="col-md-12 col-xl-12">
      <div class="card">
        <!-- Card headers -->
        <?php $this->load->view('../partials_template/list_card_header.php'); ?>
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-vcenter card-table">
            <?php echo render_table_thead($columns, true, false, true, ['sort-table' => false]); ?>
            <tbody class="ui-sortable">
              <?php if (!empty($items)) {
                $i = $from;
                foreach ($items as $key => $item) {
                  $i++;
                  $item_checkbox      = ($item['id'] == 1) ? '' : show_item_check_box('check_item', $item['id']);
                  $item_status        = ($item['id'] == 1) ? show_item_status($controller_name, $item['id'], $item['status'], 'button') : show_item_status($controller_name, $item['id'], $item['status'], 'switch');
                  $show_item_buttons  = ($item['id'] == 1) ? '' : show_item_button_action($controller_name, $item['id']);
              ?>
                <tr class="tr_<?php echo ($item['id'] == 1) ? '' : esc($item['ids']); ?>" data-id="<?php echo ($item['id'] == 1) ? '' : $item['id']; ?>">
                  <td class="text-center w-1p"><?php echo $item_checkbox; ?></td>
                  <td>
                    <div class="title"><?php echo show_high_light(esc($item['name']), $params['search'], 'name'); ?></div>
                  </td>
                  <td class="text-center w-20p">
                    <div class="title"><?php echo $item['description']; ?></div>
                  </td>
                  <td class="text-center w-10p"><?php echo $show_item_buttons; ?></td>
                </tr>
              <?php }}?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php } else {
    echo show_empty_item();
  }?>
</div>