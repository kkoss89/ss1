<?php 
  if (is_table_exists(ORDERS_REFILL)) {
    $class_refill_option = '';
  } else {
    $class_refill_option = 'd-none';
  }
?>

<div class="col-md-12 <?php echo $class_refill_option; ?>">
  <div class="form-group service-refill">
    <div class="form-label">Refill
      <label class="custom-switch">
        <span class="custom-switch-description m-r-20"><i class="fa fa-question-circle"></i></span>
        <input type="hidden" name="refill" value="0">
        <input type="checkbox" name="refill" id='refill-option' class="custom-switch-input" data-toggle="collapse" data-target="#refill-from" aria-expanded="false" aria-controls="refill" value="1" <?php echo (isset($item['refill']) && $item['refill']) ? 'checked' : '' ?>>
        <span class="custom-switch-indicator"></span>
      </label>
    </div>
  </div>
</div>
<div class="col-md-12  <?php echo $class_refill_option; ?> collapse <?php echo (isset($item['refill']) && $item['refill']) ? 'show' : '' ?>" id="refill-from">
  <div class="form-group">
    <label class="m-r-20">Refill type
      <i class="fa fa-question-circle test_popover" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="Provider - Refill status will be synchronized to with provider. If the service support a refill, user can click refill button. Refill check will not work if the service does not support this feature <br><br> Manual - Need to send order ID to provider by manual when user click refill button" data-title="Refill Type"></i>
    </label>
    <select name="refill_type" class="form-control square refill-type-option">
      <option value="0" <?php echo (isset($item['refill']) && !$item['refill']) ? 'selected' : '' ?> class='refill-manual'> Manual </option>
      <?php if(isset($item['refill']) && $item['refill']) {?>          
      <option value="1" <?php echo (isset($item['refill_type']) && $item['refill_type']) ? 'selected' : '' ?> class='refill-provider'> Provider </option>
      <?php }?>
    </select>
  </div>
</div>

<script>
  $(document).ready(function(){
    $('[data-toggle="popover"]').popover({html : true});
  });
</script>