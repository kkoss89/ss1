<div class="card">
  <div class="card-header">
    <h3 class="card-title">Publish</h3>
    <div class="card-options">
      <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
      <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
    </div>
  </div>
  <div class="card-body">
    <div class="form-body">
      <div class="row">
      <div class="col-md-12">
          <div class="form-group">
            <label for="projectinput5"><?php echo lang('Post_Category'); ?> <span class="form-required">*</span></label>
            <select name="category" class="form-control square">
              <?php if ($items_category):  ?>
                <?php foreach ($items_category as $key => $items_category): ?>
                  <option value="<?=$items_category['id']; ?>" <?=($post_category == $items_category['id']) ? 'selected' : ''; ?>><?php echo esc($items_category['name']); ?></option>
                <?php endforeach ?>
              <?php endif  ?>
            </select>
          </div>
        </div> 
        <div class="col-md-12">
          <div class="form-group">
            <label><?php echo lang('Status'); ?> <span class="form-required">*</span></label>
            <select name="status" class="form-control square">
              <option value="1" <?=($post_status) ? 'selected' : ''; ?>><?php echo lang('Active'); ?></option>
              <option value="0" <?=(!$post_status) ? 'selected' : ''; ?>><?php echo lang('Deactive'); ?></option>
            </select>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label for="projectinput5">Publication Date <span class="form-required">*</span></label>
            <input class="form-control square datepicker" name="released" type="text" value="<?=$post_released?>">
          </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
          <button type="submit" class="btn btn-primary btn-min-width mr-1 mb-1">Save</button>
        </div>
      </div>
    </div>
  </div>
</div>