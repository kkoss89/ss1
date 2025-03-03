<section class="blog-single">
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-9">
            <?php $this->load->view('/child/detail.php', ['item' => $item, 'lang_code' => $lang_code]); ?>
          </div>
          <div class="col-lg-3 mb-4 side-bar m-t-20">
            <?php $this->load->view('/child/last_posts.php', ['items' => $items_related_posts, 'lang_code' => $lang_code]); ?>
            <?php $this->load->view('/child/related_posts.php', ['items' => $items_last_posts, 'lang_code' => $lang_code]); ?>
            <?php $this->load->view('/child/post_categories.php', ['count_items_by_category' => $count_items_by_category, 'lang_code' => $lang_code]); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>