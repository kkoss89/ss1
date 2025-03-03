<?php 
$option = get_value($payment_params, 'option');
$min_amount = get_value($payment_params, 'min');
$max_amount = get_value($payment_params, 'max');
$type = get_value($payment_params, 'type');
$tnx_fee = get_value($option, 'tnx_fee');
?>

<div class="add-funds-form-content">
    <form class="form actionAddFundsForm" action="#" method="POST">
        <div class="row">
            <div class="col-md-12">
                <div class="for-group text-center">
                    <img src="<?=BASE?>/assets/images/nowpayments.png" alt="NOWPayments">
                    <p class="p-t-10">
                        <small><?=sprintf(lang("you_can_deposit_funds_with_cryptomus_they_will_be_automaticly_added_into_your_account"), 'NOWPayments')?></small>
                    </p>
                </div>
                <div class="form-group">
                    <label><?=sprintf(lang("amount_usd"), $currency_code)?></label>
                    <input class="form-control square" type="number" name="amount" 
                           step="0.1" min="<?= $min_amount ?>" max="<?= $max_amount ?>"
                           placeholder="<?= $min_amount ?>" required>
                </div>
                <div class="form-group">
                    <label><?= lang("note") ?></label>
                    <ul>
                        <?php if ($tnx_fee > 0): ?>
                        <li><?=lang("transaction_fee")?>: <strong><?= $tnx_fee ?>%</strong></li>
                        <?php endif; ?>
                        <li><?=lang("Minimal_payment")?>: <strong><?= $currency_symbol.$min_amount ?></strong></li>
                        <?php if ($max_amount > 0): ?>
                        <li><?=lang("Maximal_payment")?>: <strong><?= $currency_symbol.$max_amount ?></strong></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="form-group">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="agree" value="1" required>
                        <span class="custom-control-label text-uppercase">
                            <strong><?=lang("yes_i_understand_after_the_funds_added_i_will_not_ask_fraudulent_dispute_or_chargeback")?></strong>
                        </span>
                    </label>
                </div>
                <div class="form-actions left">
                    <input type="hidden" name="payment_id" value="<?= $payment_id ?>">
                    <input type="hidden" name="payment_method" value="<?= $type ?>">
                    <button type="submit" class="btn round btn-primary btn-min-width mr-1 mb-1">
                        <?=lang("Pay")?>
                    </button>
                </div>
            </div>  
        </div>
    </form>
</div>
