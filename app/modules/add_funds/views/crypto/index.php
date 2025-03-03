<?php 
  $option           = get_value($payment_params, 'option');
  $min_amount       = get_value($payment_params, 'min');
  $max_amount       = get_value($payment_params, 'max');
  $type             = get_value($payment_params, 'type');
  $type             = get_value($payment_params, 'type');
  $checkout_id      = get_value($option, 'checkout_id');
  $call_user_id     = base64_encode(session("uid"));
  
  $redirect_url     = cn("add_funds/crypto/complete");
?>
<style>
	[data-bxc="1"] {
		display: flex;
		justify-content: center; 
		align-items: center;  
	}
</style>
<div class="add-funds-form-content">
	<div class="row">
		<div class="col-md-12">
			<div class="for-group text-center">
				<img src="<?=BASE?>/assets/images/crypto.png" alt="Crypto" style="width:100px;">
			</div>
			<div class="form-group">
				<label><?php echo lang("note"); ?></label>
				<ul>
					<?php
					  if ($tnx_fee > 0) {
					?>
					<li><?=lang("transaction_fee")?>: <strong><?php echo $tnx_fee; ?>%</strong></li>
					<?php } ?>
					<li><?=lang("Minimal_payment")?>: <strong><?php echo $currency_symbol.$min_amount; ?></strong></li>
					<?php
					  if ($max_amount > 0) {
					?>
					<li><?=lang("Maximal_payment")?>: <strong><?php echo $currency_symbol.$max_amount; ?></strong></li>
					<?php } ?>
					<li><?php echo lang("clicking_return_to_shop_merchant_after_payment_successfully_completed"); ?></li>
				</ul>
			</div>

			<div class="form-group">
				<label class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" name="agree" value="1" required checked onclick="return false;">
					<span class="custom-control-label text-uppercase"><strong><?=lang("yes_i_understand_after_the_funds_added_i_will_not_ask_fraudulent_dispute_or_chargeback")?></strong></span>
				</label>
			</div>
			<div class="form-group">
				<div data-bxc="<?php echo $checkout_id; ?>" data-price="<?php echo $min_amount; ?>"></div>
			</div>  
		</div>  
	</div>
</div>
<script>
$(window).on('load', function () {
    setTimeout(function(){
		$('.bxc-inline').attr('data-external-reference','<?php echo $call_user_id; ?>');
	},1000);
});
</script>