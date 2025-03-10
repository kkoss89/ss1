<?php  
  $show_search_area = show_search_area($controller_name, $params);
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
    <div class="col-md-2">
      <h1 class="page-title">
          <span class="fa fa-list-ul"></span> Orders
      </h1>
    </div>
    
    <div class="col-md-12">
      <div class="row justify-content-between">
        <div class="col-md-8">
          <?php echo $show_filter_status_button; ?>
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
            <?php echo render_table_thead($columns, true, false); ?>
            <tbody>
              <?php if (!empty($items)) {
                $i = $from;
                foreach ($items as $key => $item) {
                  $i++;
                  $item_checkbox      = show_item_check_box('check_item', $item['id']);
                  $item_id            = show_item_order_id($controller_name, $item, $params);
                  $item_status        = show_item_status($controller_name, $item['id'], $item['status'], '');
                  $created            = show_item_datetime($item['created'], 'long');
                  $item_details       = show_item_order_details($controller_name, $item, $params);
                  $item_buttons       = show_item_button_action($controller_name, $item['id'], '', $item);
					
			      $isIntervalOrder    = $item['is_interval_order'];
				  if($isIntervalOrder == 1){
					  $isIntervalClass    = 'bg-yellow';
				  } else {
					  $isIntervalClass    = '';
				  }
				  
				  $isRefillClass = '';
				  $is_refill     = $item['refill'] ?? 0;
				  $refill_status = ($item['refill_status'] == 7) ? 0 : 1;
				  if($is_refill && $refill_status){
					$isRefillClass = 'style="background-color:#BBDEFB;"';
				  }
				  
              ?>
				<tr class="tr_<?php echo esc($item['ids']); ?> <?php echo $isIntervalClass; ?>" <?php echo $isRefillClass; ?>>
					<th class="text-center w-1"><?php echo $item_checkbox; ?></th>
					<td class="w-5p"><?php echo $item_id; ?></td>
					<td class="text-muted w-10p"><?php echo show_high_light(esc($item['email']), $params['search'], 'email'); ?></td>
					<td>
						<div class="title"><?php echo $item_details; ?></div>
					</td>
					<td class="text-center w-10p text-muted"><?=$created;?></td>
					<td class="text-center w-10p text-danger"><?=esc($item['note'])?></td>
					<td class="text-center w-10p"><?php echo $item_status; ?></td>
					<td class="text-center w-5p">
						<?php echo $item_buttons; ?>
					</td>
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
