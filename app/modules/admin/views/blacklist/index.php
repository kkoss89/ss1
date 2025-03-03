<?php 
  $show_search_area = show_search_area($controller_name . '_' . $params['sub_controller'] , $params);
  $show_filter_status_button = show_filter_status_button($controller_name, '', $params);
?>
<style>
  .order_btn_group .list-inline-item a.btn {
    font-size: 0.9rem;
    font-weight: 400;
  }
</style>
<div class="page-title m-b-20">
  <div class="row justify-content-between">
    <div class="col-md-6">
      <h1 class="page-title">
          <span class="fa fa-list-ul"></span> <?= $controller_title; ?>
      </h1>
    </div>
    
    <div class="col-md-12">
      <div class="row justify-content-between">
        <div class="col-md-8">
          <?php if (staff_has_permission($controller_name, 'add')): ?>
            <div class="btn-group" role="group" aria-label="Basic example">
              <a href="<?=admin_url($controller_name . "/update?type=") . $params['sub_controller'] ; ?>" class="btn btn-outline-primary ajaxModal"><span class="fe fe-plus"></span> Add new</a>
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
  <?php if(!empty($items)){
  ?>
    <div class="col-md-12 col-xl-12">
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
                  $item_checkbox = show_item_check_box('check_item', $item['ids']);

                  if ($params['sub_controller'] == 'ip') {
                    $blacklist_content = show_high_light(esc($item['ip']), $params['search'], 'ip');
                  }
                  if ($params['sub_controller'] == 'link') {
                    $blacklist_content = show_high_light(esc($item['link']), $params['search'], 'link');
                  }
                  if ($params['sub_controller'] == 'email') {
                    $blacklist_content = show_high_light(esc($item['email']), $params['search'], 'email');
                  }

                  $description = show_high_light(esc($item['description']), $params['search'], 'description');
                  $created = show_item_datetime($item['created'], 'long');
                  $item_status        = show_item_status($controller_name, $item['id'], $item['status'], '');
                  $show_item_buttons  = show_item_button_action($controller_name, $item['ids'], 'dropdown', [], ['http_build_query' => ['type' => $params['sub_controller']]]);
              ?>
                <tr class="tr_<?php echo esc($item['ids']); ?>">
                  <th class="w-1"><?php echo $item_checkbox; ?></th>
                  <td class="text-center text-muted w-10p"><?php echo $item['id']?></td>
                  <td class="w-15p"><?php echo $blacklist_content; ?></td>
                  <td class="text-center text-muted"><?php echo $description; ?></td>
                  <td class="text-center text-muted"><?php echo $item_status; ?></td>
                  <td class="text-center w-10p"><?php echo $created; ?></td>
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
