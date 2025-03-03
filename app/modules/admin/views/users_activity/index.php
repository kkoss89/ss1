<?php 
  // Page header
  echo show_page_header($controller_name, ['page-options' => 'search', 'search_params' => $params]);
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
            <?php echo render_table_thead($columns, true, true, false); ?>
            <tbody>
              <?php if (!empty($items)) {
                $i = $from;
                foreach ($items as $key => $item) {
                  $i++;
                  $item_checkbox = show_item_check_box('check_item', $item['ids']);
                  $full_name = show_high_light(esc($item['first_name']), $params['search'], 'first_name') . " " . show_high_light(esc($item['last_name']), $params['search'], 'last_name');
                  $email = show_high_light(esc($item['email']), $params['search'], 'email');
                  $ip_address = show_high_light(esc($item['ip']), $params['search'], 'ip');
                  $created = show_item_datetime($item['created'], 'long');
                  $item_activity = show_item_activity($item['type']);
              ?>
                <tr class="tr_<?php echo esc($item['ids']); ?>">
                  <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
                  <td class="text-center text-muted w-10p"><?php echo $item['id']?></td>
                  <td>
                    <div class="title"><h6><?php echo $full_name; ?></h6></div>
                    <div class="sub text-muted"><?php echo $email; ?></small></div>
                  </td>
                  <td class="text-center w-10p"><?php echo $item_activity; ?></td>
                  <td class="text-center text-muted w-15p"><?php echo $ip_address; ?></td>
                  <td class="text-center w-10p"><?php echo $item['country']; ?></td>
                  <td class="text-center text-muted w-10p"><?php echo $created; ?></td>
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
