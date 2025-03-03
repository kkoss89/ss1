<?php 
// Page header
echo show_page_header($controller_name, ['page-options' => 'add-new-service-comment', 'page-options-type' => 'ajax-modal', 'service_id' => $service_id, 'sub_page_title' => 'Comments']);
// Page header Filter
//echo show_page_header_filter($controller_name, ['params' => $params]);

?>

<div class="row">
    <?php if (!empty($items)) {
        ?>
        <div class="col-md-12 col-xl-12">
            <div class="card">
                <!-- Card headers -->
                <?php $this->load->view('../partials_template/list_card_header.php'); ?>
                <div class="table-responsive ">
                    <table class="table table-hover table-bordered table-vcenter card-table">
                        <?php echo render_table_thead($columns, true, true, true, ['sort-table' => false]); ?>
                        <tbody>
                            <?php
                            if (!empty($items)) {
                                $i = $current_page * 1000;
                                foreach ($items as $key => $item) {
                                    $i++;
									$item_checkbox = show_item_check_box('check_item', $item['id']);
							?>
                                    <tr class="tr_<?php echo esc($item['id']); ?>" data-id="<?php echo $item['id']; ?>">
										<td class="text-center w-1p"><?php echo $item_checkbox; ?></td>
                                        <td class="text-center"><?php echo $i; ?></td>
                                        <td>
                                            <div class="title"><?php echo show_high_light(esc($item['comment']), $params['search'], 'comment'); ?></div>
                                        </td>
                                        <td class="text-center w-10p"><?php echo ($item['created_at']) ? date("d-M-Y h:i A",strtotime($item['created_at'])) : ''; ?></td>
                                        <td class="text-center w-10p"><?php echo ($item['updated_at']) ? date("d-M-Y h:i A",strtotime($item['updated_at'])) : ''; ?></td>
										<td class="text-center w-10p">
											<div class="item-action dropdown">
												<a href="javascript:void(0)" data-toggle="dropdown" class="icon" aria-expanded="false">
													<i class="fe fe-more-vertical"></i>
												</a>
												<div class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(44px, -1px, 0px); top: 0px; left: 0px; will-change: transform;">
													<a href="<?php echo base_url('admin/services/update_service_comment/'.$item['service_id'].'/'.$item['id']); ?>" class="dropdown-item ajaxModal" data-confirm_ms="">
														<i class="dropdown-icon fe fe-edit"></i> Edit
													</a>
													<a href="<?php echo base_url('admin/services/delete_comment/'.$item['id']); ?>" class="dropdown-item ajaxDeleteItem" data-confirm_ms="Are you sure you want to delete this item">
														<i class="dropdown-icon fe fe-trash-2"></i> Delete
													</a>
												</div>
											</div>
										</td>
                                    </tr>
							<?php }
							} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
		<?php echo show_pagination($pagination); ?>
    <?php
    } else {
        echo show_empty_item();
    }
    ?>
</div>