<div class="card">
  <div class="card-header">
    <h3 class="card-title">Page seo informations</h3>
    <div class="card-options">
      <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
      <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
    </div>
  </div>
  <div class="card-body">
    <div class="form-body">
      <div class="row">
        <div class="col-md-12">
          <small class="text-danger">Note if you want use default informations in settings page then leave these informations fields empty</small>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="form-group">
            <label>Meta keywords</label>
            <input class="form-control square" name="meta_keywords" type="text" data-role="tagsinput" value="<?=$post_meta_keywords;?>">
          </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="form-group">
            <label><?php echo lang('meta_description'); ?></label>
            <textarea rows="3" class="form-control square" name="meta_description"><?=$post_meta_description;?></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>                  
<script>
  $("input").val();
</script> 