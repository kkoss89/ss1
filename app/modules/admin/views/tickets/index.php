
<?php 
  $show_search_area = show_search_area($controller_name, $params);
?>
<div class="page-title m-b-20">
  <div class="row justify-content-between">
    <div class="col-md-6">
      <h1 class="page-title">
          <span class="fa fa-comments-o"></span> Ticket
      </h1>
    </div>
    <div class="col-md-12">
      <div class="row justify-content-between">
        <div class="col-md-6">
          <?php
            if (staff_has_permission($controller_name, 'add')) :
          ?>
          <div class="btn-group" role="group" aria-label="Basic example">
            <a href="<?=admin_url($controller_name . "/add_ticket"); ?>" class="btn btn-outline-primary ajaxModal"><span class="fe fe-plus"></span> Add Ticket</a>
          </div>
          <?php endif; ?>
        </div>
        <div class="col-md-4 search-area">
          <?php echo $show_search_area; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <?php if (!empty($items)) {
  ?>
    <div class="col-md-12 col-xl-12">
      <div class="card">
        <!-- Card headers -->
        <?php $this->load->view('../partials_template/list_card_header.php'); ?>
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-vcenter card-table">
            <?php echo render_table_thead($columns, true, false); ?>
            <tbody>
              <?php if (!empty($items)) {
                $i = $from;
                foreach ($items as $key => $item) {
                  $i++;
                  $item_checkbox      = show_item_check_box('check_item', $item['id']);
                  $item_status        = show_item_status($controller_name, $item['id'], $item['status'], '');
                  $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
                  $created            = show_item_datetime($item['created'], 'long');
                  $subject            = show_item_ticket_subject($controller_name, $item, $params);
              ?>
                <tr class="tr_<?php echo esc($item['ids']); ?>">
                  <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
                  <td class="text-center w-5p text-muted"><?php echo show_high_light(esc($item['id']), $params['search'], 'id'); ?></td>
                  <td class="text-center w-15p text-muted"><?php echo show_high_light(esc($item['email']), $params['search'], 'email'); ?></td>
                  <td><?php echo $subject; ?></td>
                  <td class="text-center w-10p"><?php echo $item_status; ?></td>
                  <td class="text-center w-15p text-muted"><?php echo $created; ?></td>
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
