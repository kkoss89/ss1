<?php 
  // Page header
  echo show_page_header($controller_name, ['page-options' => '', 'search_params' => $params]);
?>
<div class="row">
    <div class="col-md-12 col-xl-12">
      <div class="card">
        <div class="card-header">
          <ul class="list-inline mb-0 order_btn_group">
            <?php 
              foreach ($report_filters as $key => $item_field) {
                $class_item = ($task == $key) ? 'btn-primary' : '';
                echo sprintf('<li class="list-inline-item"><a class="btn %s" href="?type=%s">%s</a>', $class_item, $key, $item_field['name']);
              }
            ?>
          </ul>
        </div>
        <div class="table-responsive">
          <table class="table table-hover table-vcenter card-table">
            <?php 
              echo render_table_thead($columns, false, false, false); 
              echo render_report_tbody($controller_name, ['data_reports' => $data_reports, 'data_type' => 'model'], ['task' => $task]); 
            ?>
          </table>
        </div>
      </div>
    </div>
</div>
