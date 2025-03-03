<section class="blog">
  <div class="container">
    <div class="row ">
      <div class="col-md-12 row justify-content-md-center">
        <div class="col-md-8">
          <div class="blog-header">
            <div class="title">
              <h1 class="title-name"><?=lang('Blog')?></h1>
            </div>
            <span class="text-muted"><?php echo lang("we_bring_you_the_best_stories_and_articles_youll_find_tips_on_all_social_networks_growth_and_general_social_media_advice_as_well_as_latest_updates_related_to_our_services"); ?></span>
          </div>
        </div>
      </div>
      <?php
        $author = get_option('website_name');
      ?>
      <?php if (!empty($items)) : ?>
        <?php foreach ($items as $key => $item) : ?>
          <?php 
            $item_link_detail = cn('blog/' . $item['url_slug']);
            $item_link_related_category = cn('blog/category/' . strip_tags($item['category_url_slug']));
            $limit_string = ($lang_code == 'en') ? 69 : 18;
            $item_title = truncate_string(strip_tags($item['name']), $limit_string);
            $limit_string = ($lang_code == 'en') ? 200 : 50;
            $item_content = truncate_string(strip_tag_css($item['content'], 'html'), $limit_string);
            $item_released = show_item_post_released_time($item['released']);
            $item_category_name = show_category_name_by_lang_code($item, $lang_code);
          ?>
          <div class="col-md-6 col-sm-12">
            <div class="card blog-item">
              <div class="box-image">
                <a href="<?= $item_link_detail ?>">
                  <img class="img-fluid" src="<?= $item['image'] ?>" alt="<?= $item['url_slug'] ?>">
                </a>
              </div>
              <div class="content">
                <h4 class="title">
                  <a href="<?= $item_link_detail ?>"><?= $item_title ?></a>
                </h4>
                <div class="short-desc"> <?= $item_content ;?> </div>
                <div class="d-flex align-items-center mt-auto">
                  <div> <?=lang('by')?> <a href="javascript:void(0)" class="text-default"><?= esc($author); ?></a>
                    <small class="d-block text-muted">
                      <i class="fa fa-calendar"></i> <?= $item_released ?> </small>
                  </div>
                  <div class="ml-auto text-muted">
                    <a class="icon ml-3">
                      <i class="fa fa-tag"></i>
                    </a>
                    <a href="<?= $item_link_related_category ?>"> <?= $item_category_name ?></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach ?>
        <!-- Pagination -->
        <div class="col-md-12 m-t-30 justify-content-center">
          <?php echo show_pagination($pagination, ''); ?>
        </div> 
      <?php else : ?>
        <?= show_empty_item(); ?>  
      <?php endif ?>
    </div>
  </div>
</section>