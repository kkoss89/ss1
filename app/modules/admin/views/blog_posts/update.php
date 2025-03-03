<?php
  $post_image             = '';
  $post_lang_code         = '';
  $post_url_slug          = '';
  $post_category          = '';
  $post_status            = 0;
  $post_name              = '';
  $post_content           = '';
  $post_meta_keywords     = '';
  $post_meta_description  = '';
  $post_released = '';
  if ($task == 'edit') {
    $post_image             = $item['image'];
    $post_url_slug          = $item['url_slug'];
    $post_category          = $item['cate_id'];
    $post_status            = $item['status'];
    $post_released          = date("d/m/Y",  strtotime(@$item['released']));
    $post_name              = $item_post_lang['name'];
    $post_content           = $item_post_lang['content'];
    $post_meta_keywords     = $item_post_lang['meta_keywords'];
    $post_meta_description  = $item_post_lang['meta_description'];
  }
  $modal_title = 'Add New Post';
  $redirect_url    = admin_url($controller_name);
  if (!empty($item['id'])) {
    $ids = $item['id'];
    $modal_title = 'Edit Post';
    $redirect_url    = get_current_url();
  }
  $form_url        = admin_url($controller_name."/store/");
  
  $form_attributes = ['class' => 'form actionForm', 'data-redirect' => $redirect_url, 'method' => "POST"];
  $form_hidden     = [
    'id'             => @$item['id'],
    'item_lang_code' => $lang_code,
    'id_post_lang'   => @$item_post_lang['id']
  ];
  
?>
<style>
  .blog-post-note {
    background-color: #c0edf1 !important;
    border-color: #664dc9 !important;
    color: #000 !important;
    border-left: 5px solid #664dc9 !important;
    border-radius: 0 4px 4px 0 !important;
    margin: 0 0 20px !important;
    padding: 15px 30px 15px 15px !important;
  }
</style>
<?php echo form_open($form_url, $form_attributes, $form_hidden); ?>
  <div class="row">
    <div class="col-md-9">
      <div class="blog-post-note" role="alert">
        <?=$lang_mode_note?>
      </div>
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><?=$modal_title?></h3>
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
                  <label for="projectinput5"><?php echo lang('article_title'); ?> <span class="form-required">*</span></label>
                  <input class="form-control square" name="name" type="text" value="<?=$post_name;?>">
                </div>
              </div>
              <?php if ($task == 'edit'): ?>
              <div class="col-md-12">
                <div class="form-group">
                  <label>URL Slug <span class="form-required">*</span></label>
                  <div class="input-group">
                    <span class="input-group-prepend" id="basic-addon3">
                      <span class="input-group-text text-muted"><?php echo cn('blog/'); ?></span>
                    </span>
                    <input type="text" name="url_slug" class="form-control" value="<?=$post_url_slug;?>">
                  </div>
                </div> 
              </div>
              <?php endif ?>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="projectinput5"><?php echo lang('Image_thumbnail'); ?> <span class="form-required">(900 x 500px)*</span></label>
                  <div class="input-group">
                    <input type="text" name="image" class="form-control" value="<?=$post_image;?>">
                    <span class="input-group-append btn-elFinder">
                      <button class="btn btn-info" type="button">
                        <i class="fe fe-image">
                        </i>
                      </button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="bloginput8">Content <span class="form-required">*</span></label>
                  <textarea id="editor" rows="2" class="form-control square" name="content" placeholder="Write conetnt in here"><?=$post_content;?></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Page Seo -->
      <?php $this->load->view('/child_update/page_seo.php', ['post_meta_keywords' => $post_meta_keywords, 'post_meta_description' => $post_meta_description]); ?>                 
    </div> 
    <div class="col-md-3">
      <!-- Publish -->
      <?php 
        $this->load->view('/child_update/publish.php', [
          'items_category' => $items_category, 
          'post_category'  => $post_category, 
          'post_status'    => $post_status, 
          'post_released'  => $post_released
        ]); 
      ?>
      <!-- Language -->
      <?php
        if ($task == 'edit') {
          $items_lang = array_sort_by_new_key($items_lang, 'code');
          if (isset($items_lang[$lang_code])) {
            unset($items_lang[$lang_code]);
          }
          if (!empty($items_lang)) {
            $this->load->view('/child_update/language_version.php',[
              'controller_name' => $controller_name,
              'post_id'         => $item['id'],
              'items_lang'      => $items_lang,
            ]);
          }
        } 
      ?>
    </div>
  </div>
<?php echo form_close(); ?>
<script>
  "use strict";
  $(document).ready(function() {
    plugin_editor('#editor', {append_plugins: 'image  media', height: 500});
    $(document).on('click','.btn-elFinder', function(){
      var _that = $(this);
      getPathMediaByelFinderBrowser(_that);
    });
  });

  // Convert to Url Slug
  // $(document).on("keyup", "input[type=text][name=name]", function(){
  //   var _that  = $(this),
  //     _value = _that.val();
  //     _value = convertToSlug(_value);
  //   $("input[name=url_slug]").val(_value);  
  // });

  // function convertToSlug(Text) {
  //   return Text
  //       .toLowerCase()
  //       .replace(/ /g,'-')
  //       .replace(/[^\w-]+/g,'')
  //       ;
  // }
</script>

<script>
  $(function(){
    $('.datepicker').datepicker({
      format: "dd/mm/yyyy",
      orientation: 'bottom',
      autoclose: true,
    });
    <?php if (empty($post_released) ) { ?>
      $(".datepicker").datepicker().datepicker("setDate", new Date());
    <?php } ?>
    function truncateDate(date) {
      return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }
  });
</script>

