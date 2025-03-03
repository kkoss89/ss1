<div id="main-modal-content">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header bg-pantone">
				<h4 class="modal-title"><i class="fa fa-eye"></i> View Sub Orders</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6 col-xl-6 mb-3">
						<center><b>First Order Placed at :</b> <strong><?php echo (!empty($main_item) && $main_item['start_at'] != null) ? date("Y-m-d h:i:s A",strtotime($main_item['start_at'])) : '------'; ?></strong></center>
					</div>
					<div class="col-md-6 col-xl-6 mb-3">
						<center><b>Last Order Ends at :</b> <strong><?php echo (!empty($main_item) && $main_item['end_at'] != null) ? date("Y-m-d h:i:s A",strtotime($main_item['end_at'])) : '------'; ?></strong></center>
					</div>
				<?php 
				if(!empty($items)){
				?>
					<div class="col-md-12 col-xl-12">
						<div class="card">
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-vcenter card-table">
									<?php echo render_table_thead($columns, false, false, false); ?>
									<tbody>
									<?php 
										if (!empty($items)) {
											$i = $from;
											foreach ($items as $key => $item) {
												$i++;
												$item_checkbox      = show_item_check_box('check_item', $item['id']);
												$item_id            = show_item_order_id($controller_name, $item, $params);
												$item_status        = show_item_status($controller_name, $item['id'], $item['status'], '');
												$created            = show_item_datetime($item['created'], 'long');
												if($item['start_at']){
													$start_at       = show_item_datetime($item['start_at'], 'long');	
												} else {
													$start_at       = 'Not Started Yet';
												}
												if($item['changed']){
													$changed       = show_item_datetime($item['changed'], 'long');	
												} else {
													$changed       = '---';
												}
												
												$item_details       = show_item_order_details($controller_name, $item, $params, 'suborders');
												$item_buttons       = show_item_button_action($controller_name, $item['id'], '', $item);
													
												$isIntervalOrder    = $item['is_interval_order'];
												if($isIntervalOrder == 1){
													$isIntervalClass    = 'bg-yellow';
												} else {
													$isIntervalClass    = '';
												}
									?>
											<tr class="tr_<?php echo esc($item['ids']); ?>">
												<td class="w-5p"><?php echo $item_id; ?></td>
												<td>
													<div class="title"><?php echo $item_details; ?></div>
												</td>
												<td class="text-center w-10p text-muted"><?=$created;?></td>
												<td class="text-center w-10p text-muted"><?=$start_at;?></td>
												<td class="text-center w-10p text-muted"><?=$changed;?></td>
												<td class="text-center w-10p text-danger"><?=esc($item['note'])?></td>
												<td class="text-center w-10p"><?php echo $item_status; ?></td>
											</tr>
									<?php 
											}
										}
									?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				<?php 
				} else {
					echo show_empty_item();
				}
				?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>