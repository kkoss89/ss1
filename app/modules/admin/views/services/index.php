<?php  
  $show_search_area = show_search_area($controller_name, $params);
  $show_items_sort_by_cateogry = show_items_sort_by_category($controller_name, $items_category, $params);
?>
<div class="page-title m-b-20">
  <div class="row justify-content-between">
    <div class="col-md-2">
      <h1 class="page-title">
          <span class="fa fa-list-ul"></span> Services
      </h1>
    </div>
    <div class="col-md-4 search-area">
      <?php echo $show_search_area; ?>
    </div>
    <div class="col-md-12">
      <div class="row justify-content-between">
        <div class="col-md-6">
          <div class="btn-group" role="group" aria-label="Basic example">
            <?php if (staff_has_permission($controller_name, 'add')) : ?>
              <a href="<?=admin_url($controller_name . "/update"); ?>" class="btn btn-outline-primary ajaxModal"><span class="fe fe-plus"></span> Add new</a>
            <?php endif; ?>
            <?php if (staff_has_permission($controller_name, 'import')) : ?>
              <a href="<?=admin_url('provider/services');?>" class="btn btn-outline-primary"><span class="fe fe-folder-plus"></span> Import</a>
            <?php endif; ?>
            <a href="#" class="btn btn-outline-primary btn-services-collapse"><span class="fe fe-chevrons-up"></span> Hide All</a>
          </div>
        </div>
        <div class="col-md-4 d-flex">
          <?php echo $show_items_sort_by_cateogry; ?>
          <?php echo show_bulk_btn_action($controller_name); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <?php if (!empty($items)) : ?>
    <?php foreach ($items as $key => $items_category) : ?>
      <?php 
        $item_category_id = $items_category[0]['cate_id'];
        $link_edit_category = admin_url('category/update/' . $item_category_id);
      ?>
      <div class="col-md-12 col-xl-12 items-by-category">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title d-flex align-items-center">
				<?php 
				  // If a category image is not set, default to a FontAwesome icon class.
				  $categoryImage = !empty($items_category[0]['category_image']) 
								   ? $items_category[0]['category_image'] 
								   : 'fa-solid fa-circle';

				  // Check if the provided value is a FontAwesome icon class.
				  if (strpos($categoryImage, 'fa-') !== false) {
					  // Render the icon using an <i> tag.
					  echo '<i class="mr-1 ' . esc($categoryImage) . '"></i>';
				  } else {
					  // Otherwise, assume it's an image URL and display it within an <img> tag.
					  echo '<i class="mr-1 ' . esc($categoryImage) . '"></i>';
				  }
				?>	
              <?=$key;?>
              <a href="<?php echo $link_edit_category; ?>" class="badge badge-default ajaxModal" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Category"><i class="fe fe-edit-2"></i></a>
            </h4>
            <div class="card-options">
              <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
              <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
            </div>
          </div>
          <div class="table-responsive service-sortable">
            <table class="table table-hover table-bordered table-vcenter card-table">
              <?php 
                $thead_params = [
                  'sort-table' => true,
                  'checkbox_data_name' => 'check_' . $items_category[0]['cate_id'],
                ];
                if (!staff_has_permission($controller_name, 'see_provider')) {
                  unset($columns['provider']);
                }
                echo render_table_thead($columns, true, false, true, $thead_params); 
              ?>
              <tbody>
                <?php if (!empty($items_category)) : ?>
                  <?php foreach ($items_category as $key => $item) : ?>
                    <?php
                      $item_checkbox      = show_item_check_box('check_item', $item['id'], '', 'check_' . $item['cate_id']);
                      $item_status_type = (staff_has_permission($controller_name, 'change_status')) ? 'switch' : 'button';
                      $item_status        = show_item_status($controller_name, $item['id'], $item['status'], $item_status_type);
                      $show_item_buttons  = show_item_button_action($controller_name, $item['id'], '', $item);
                      $show_item_view     = show_item_details($controller_name, $item);
                      $show_item_attr     = show_item_service_attr($item);
                    ?>
                    <tr class="tr_<?php echo esc($item['ids']); ?>" data-id="<?php echo $item['id']; ?>" data-cate_id="<?php echo $item['cate_id']; ?>">
                      <td class="sort-handler w-1p"><i class="fe fe-grid"></i></td>
                      <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
                      <td class="text-center w-5p text-muted"><?=show_high_light(esc($item['id']), $params['search'], 'id');?></td>
                      <td>
                        <div class="title"><?php echo show_high_light(esc($item['name']), $params['search'], 'name'); ?></div>
                      </td>
                      <?php if (staff_has_permission($controller_name, 'see_provider')) : ?>
                        <td class="text-center w-10p  text-muted">
                          <?php
                            echo ($item['add_type'] == "api") ? truncate_string($item['api_name'], 13) : 'manual';
                          ?>
                          <div class="text-muted small">
                            <?=(!empty($item['api_service_id'])) ? show_high_light(esc($item['api_service_id']), $params['search'], 'api_service_id') : ""?>
                          </div>
                        </td>
                      <?php endif;?>
                      <td class="text-center w-10p">
                        <?php 
                          echo $item['type'];
                          echo $show_item_attr;
                        ?>
                      </td>
                      <td class="text-center w-5p">
                        <div><?=(double)$item['price'];?></div>
                        <?php 
                          if (isset($item['original_price']) && staff_has_permission($controller_name, 'see_provider')) {
                            $text_color = ($item['original_price'] > $item['price']) ? "text-danger" : "text-muted";
                            echo '<small class="'.$text_color.'">'.(double)$item['original_price'].'</small>';
                          }
                        ?>
                      </td>
                      <td class="text-center w-10p text-muted"><?=$item['min'] . ' / ' . $item['max']?></td>
                      <td class="text-center w-5p"> <?php echo $show_item_view;?></td>
                      <td class="text-center w-5p"><?php echo $item_status; ?></td>
                      <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
                    </tr>
                  <?php endforeach ?>
                <?php endif ?> 
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endforeach ?>                    
  <?php else : ?>
    <?php echo show_empty_item(); ?>
  <?php endif ?>
</div>
