<?php
  $form_url = admin_url($controller_name."/store/");
  $form_attributes = array('class' => 'form actionForm', 'data-redirect' => get_current_url(), 'method' => "POST");
?>
<div class="card content">
  <div class="card-header">
    <h3 class="card-title"><i class="fe fe-mail"></i> Affiliate System</h3>
  </div>
  <?php echo form_open($form_url, $form_attributes); ?>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12 col-lg-12">
          <div class="form-group">
            <div class="form-label">Affiliate mode</div>
            <div>
              <label class="custom-switch">
                <input type="hidden" name="affiliate_mode" value="0">
                <input type="checkbox" name="affiliate_mode" class="custom-switch-input" <?=(get_option("affiliate_mode", 0) == 1) ? "checked" : ""?> value="1">
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description"> Active</span>
              </label>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Commission rate, %</label>
                <input class="form-control" name="affiliate_commission_rate" value="<?php echo get_option("affiliate_commission_rate", 2)?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Minimum payout</label>
                <input class="form-control" name="affiliate_minimum_payout" value="<?php echo get_option("affiliate_minimum_payout", 10)?>">
              </div>
            </div>
          </div>

          <h5><i class="fe fe-link"></i> Affiliate Instruction</h5>
          <div class="form-group">
            <label class="custom-switch">
              <input type="hidden" name="enable_affiliate_instruction" value="0">
              <input type="checkbox" name="enable_affiliate_instruction" class="custom-switch-input" <?=(get_option("enable_affiliate_instruction", 0) == 1) ? "checked" : ""?> value="1">
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">Active</span>
            </label>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
              <label class="form-label">Content</label>
              <textarea rows="3" name="affiliate_instruction_content" class="form-control plugin_editor"><?=get_option('affiliate_instruction_content', "<h2>Affiliate System</h2><p>When you invite new users by referral link, you will get commissions from all their payments. Then you may request payouts when you save the minimum payout.</p>")?>
              </textarea>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer text-end">
      <button class="btn btn-primary btn-min-width text-uppercase">Save</button>
    </div>
  <?php echo form_close(); ?>
</div>

<script>
  $(document).ready(function() {
    plugin_editor('.plugin_editor', {height: 400});
  });
</script>