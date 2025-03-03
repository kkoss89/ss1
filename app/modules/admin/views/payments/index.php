<?php 
  // Page header
  echo show_page_header($controller_name, ['page-options' => '']);
  // Page header Filter
  echo show_page_header_filter($controller_name, ['items_status_count' => $items_status_count, 'params' => $params]);
?>

<div class="row">
  <?php if(!empty($items)){
  ?>
    <div class="col-md-12">
      <div class="card">
        <!-- Card headers -->
        <?php $this->load->view('../partials_template/list_card_header.php'); ?>
        <div class="table-responsive sortable-content">
          <table class="table table-hover table-bordered table-vcenter card-table" data-sort_table_url="<?=admin_url($controller_name . '/' . 'sort_table')?>">
            <?php echo render_table_thead($columns, true, false, true, ['sort-table' => true]); ?>
            <tbody>
              <?php if (!empty($items)) {
                $i = $from;
                foreach ($items as $key => $item) {
                  $i++;
                  $item_checkbox      = show_item_check_box('check_item', $item['id']);
                  $item_allowed_user  = show_item_status($controller_name, $item['id'], $item['new_users']);
                  $item_status        = show_item_status($controller_name, $item['id'], $item['status'], 'switch');
                  $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
              ?>
                <tr class="tr_<?php echo esc($item['id']); ?>" data-id="<?php echo $item['id']; ?>">
                  <td class="sort-handler w-1p"><i class="fe fe-grid"></i></td>
                  <th class="text-center"><?php echo $item_checkbox; ?></th>
                  <td class="text-muted w-5p"><?php echo ucfirst(str_replace('_', " ",$item['type']))?></td>
                  <td>
                    <div class="title"><?php echo show_high_light(esc($item['name']), $params['search'], 'name'); ?></div>
                  </td>
                  <td class="text-center w-5p"><?php echo esc($item['min']); ?></td>
                  <td class="text-center w-5p"><?php echo esc($item['max']); ?></td>
                  <td class="text-center w-5p"><?php echo $item_allowed_user; ?></td>
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
  <?php }else{
    echo show_empty_item();
  }?>
</div>
