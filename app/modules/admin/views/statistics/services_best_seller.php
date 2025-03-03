<?php

    $columns     =  array(
        "id"             => ['name' => 'Service ID',    'class' => 'text-center'],
        "name"           => ['name' => 'Name',    'class' => ''],
        "total_order"    => ['name' => 'Total orders', 'class' => 'text-center'],
        "provider"       => ['name' => 'provider',    'class' => 'text-center'],
        "type"           => ['name' => 'type',    'class' => 'text-center'],
        "rate"           => ['name' => 'Rate per 1k', 'class' => 'text-center'],
        "min_max"        => ['name' => 'Min/Max', 'class' => 'text-center'],
        "description"    => ['name' => 'description', 'class' => 'text-center'],
    );
?>
<div class="row">
    <div class="col-md-12 col-xl-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Top best sellers</h4>
          <div class="card-options">
            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-vcenter card-table">
            <?php 
              echo render_table_thead($columns, false, false, false); 
            ?>
            <tbody>
              <?php if (!empty($items_best_seller)) {
                foreach ($items_best_seller as $key => $item) {
                  $item_checkbox      = show_item_check_box('check_item', $item['id'], '', 'check_' . $item['cate_id']);
                  $show_item_view     = show_item_details('services', $item);
                  $show_item_attr     = show_item_service_attr($item);
              ?>
                <tr class="tr_<?php echo esc($item['ids']); ?>">
                  <td class="text-center w-5p text-muted"><?=$item['id'];?></td>
                  <td>
                    <div class="title"><?php echo esc($item['name']); ?></div>
                  </td>
                  <td class="text-center w-5p"> <?php echo $item['total_orders'];?></td>
                  <td class="text-center w-10p  text-muted">
                    <?php
                      echo ($item['add_type'] == "api") ? truncate_string($item['api_name'], 13) : 'manual';
                    ?>
                    <div class="text-muted small">
                      <?=(!empty($item['api_service_id'])) ? esc($item['api_service_id']) : ""?>
                    </div>
                  </td>
                  <td class="text-center w-10p">
                    <?php 
                      echo $item['type'];
                      echo $show_item_attr;
                    ?>
                  </td>
                  <td class="text-center w-5p">
                    <div><?=(double)$item['price'];?></div>
                    <?php 
                      if (isset($item['original_price'])) {
                        $text_color = ($item['original_price'] > $item['price']) ? "text-danger" : "text-muted";
                        echo '<small class="'.$text_color.'">'.(double)$item['original_price'].'</small>';
                      }
                    ?>
                  </td>
                  <td class="text-center w-10p text-muted"><?=$item['min'] . ' / ' . $item['max']?></td>
                  <td class="text-center w-5p"> <?php echo $show_item_view;?></td>
                </tr>
              <?php }}?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>
