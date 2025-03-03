<?php 
  $show_search_area = show_search_area($controller_name, $params);
  $show_items_sort_by_cateogry = show_items_sort_by_category($controller_name, $items_category, $params);
?>
<div class="page-title m-b-20">
  <div class="row justify-content-between">
    <div class="col-md-2">
      <h1 class="page-title">
          <span class="fa fa-list-ul"></span> Blog Posts
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
              <a href="<?=admin_url($controller_name . "/update"); ?>" class="btn btn-outline-primary"><span class="fe fe-plus"></span> Add New Post</a>
            <?php endif;?>
            <?php if (staff_has_permission('blog_category', 'add')) : ?>
              <a href="<?=admin_url("blog_category/update"); ?>" class="btn btn-outline-primary ajaxModal"><span class="fe fe-plus"></span> Add Blog Category</a>
            <?php endif;?>
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
  <?php if(!empty($items)){
    foreach ($items as $key => $items_category) {
  ?>
    <div class="col-md-12 col-xl-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title"><?=$key;?></h4>
          <div class="card-options">
            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-vcenter card-table">
            <?php 
              $thead_params = [
                'checkbox_data_name' => 'check_' . $items_category[0]['cate_id']
              ];
              echo render_table_thead($columns, true, false, true, $thead_params); 
            ?>
            <tbody>
              <?php if (!empty($items_category)) {
                foreach ($items_category as $key => $item) {
                  $item_checkbox      = show_item_check_box('check_item', $item['id'], '', 'check_' . $items_category[0]['cate_id']);
                  $item_status        = show_item_status($controller_name, $item['id'], $item['status'], 'switch');
                  $show_item_buttons  = show_item_button_action($controller_name, $item['id']);

                  $language_name = '';
                  foreach ($items_lang as $key => $item_lang) {
                    if ($item_lang['is_default']) {
                      $link = admin_url($controller_name . '/update/'. $item['id']);
                      $language_name .= sprintf('<a class="m-r-10" href="%s" data-toggle="tooltip" title="Edit"><i class="fa fa-check text-success"></i></a>', $link);
                    } else {
                      $link = admin_url($controller_name . '/update/'. $item['id'] .'?ref_lang='.$item_lang['code']);
                      $language_name .= sprintf('<a href="%s" data-toggle="tooltip" title="Edit related language for this item"><span class="fe fe-edit"></span></a>', $link);
                    }
                  }
              ?>
                <tr class="tr_<?php echo esc($item['ids']); ?>" data-id="<?php echo $item['id']; ?>">
                  <td class="text-center w-1p"><?php echo $item_checkbox; ?></td>
                  <td>
                    <div class="title"><?php echo show_high_light(esc($item['name']), $params['search'], 'name'); ?></div>
                  </td>
                  <td class="text-center w-10p"><?php echo $item_status; ?></td>
                  <td class="text-center w-10p"><?php echo $language_name; ?></td>
                  <td class="text-center w-10p"><?php echo show_item_datetime($item['released'], 'short'); ?></td>
                  <td class="text-center w-10p"><?php echo show_item_datetime($item['changed'], 'long'); ?></td>
                  <td class="text-center w-10p"><?php echo show_item_datetime($item['created'], 'long'); ?></td>
                  <td class="text-center w-10p"><?php echo $show_item_buttons; ?></td>
                </tr>
              <?php }}?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php }}else{
    echo show_empty_item();
  }?>
</div>
