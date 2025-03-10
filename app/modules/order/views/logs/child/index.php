<?php if(!empty($items)){ 
?>
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
        <h3 class="card-title"><?=lang("Lists")?></h3>
        <div class="card-options">
            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
        </div>
        </div>
        <div class="table-responsive">
        <table class="table table-hover table-bordered table-vcenter card-table">
            <thead>
                <?php echo render_table_thead($columns, false, false, false, false); ?>
            </thead>
            <tbody>
            <?php if (!empty($items)) {
                foreach ($items as $key => $item) {
                $params['search']['field'] = 'id';
				
				if(isset($item['order_id']) && $item['order_id'] != ''){
					$item_id = show_high_light(esc($item['order_id']), $params['search'], 'order_id');    
				} else {
					$item_id = show_high_light(esc($item['id']), $params['search'], 'id');    
				}
				
                $item_service_name = $item['service_id'] ." - ". $item['service_name'];
                $item_details       = show_item_order_details($controller_name, $item, $params, 'user');
                $item_status = (in_array($item['status'], ['error'])) ? 'pending' : $item['status'];
            ?>
            <tr class="tr_<?=$item['ids']?>">
                <td class="text-center"><?=$item_id; ?></td>
                <td>
                    <?php echo $item_details; ?>
                </td>
                <td class="text-center w-10p"><?=convert_timezone($item['created'], "user")?></td>
                <td class="text-center w-10p"><?php echo show_item_status($controller_name, $item['id'], $item_status, '', 'user');?></td>
                <?php
                    if (is_table_exists(ORDERS_REFILL)) {
                        echo show_item_refill_button($controller_name, $item);
                    }
                ?>    
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
