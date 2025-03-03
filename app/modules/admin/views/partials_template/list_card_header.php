<div class="card-header massAction">
    <h3 class="card-title"><?=lang("Lists")?></h3>
    <div class="btnActions d-none btn-group" role="group" aria-label="Basic example">
        <span class="btn btn-primary m-r-2 number-items-selected"> 00 items selected</span>
        <?php
			if(isset($params['service_id'])){
				echo show_bulk_btn_action($controller_name.'/comments'); 
			} elseif (isset($params['sub_controller'])) {
                echo show_bulk_btn_action($controller_name, ['http_build_query' => ['type' => $params['sub_controller']]]); 
            } else {
                echo show_bulk_btn_action($controller_name); 
            }
        ?>
    </div>
    <div class="card-options">
        <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
    </div>
</div>