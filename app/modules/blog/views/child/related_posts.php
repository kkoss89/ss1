
<?php if (!empty($items)) : ?>
  <div class="widget">
    <h3 class="title"><?=lang('related_posts')?></h3>
    <?php foreach ($items as $key => $item) : ?>
      <?php
        $item_link_detail = cn('blog/' . $item['url_slug']);
        $limit_string = ($lang_code == 'en') ? 69 : 18;
        $item_title = truncate_string(strip_tags($item['name']), $limit_string);
      ?>
      <div class="blog-item">
        <div class="box-image">
          <a href="<?=$item_link_detail?>">
            <img class="img-fluid" src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
          </a>
        </div>
        <div class="content m-t-10">
          <p>
            <a href="<?= $item_link_detail ?>"><?= $item_title; ?></a>
          </p>
        </div>
      </div>
    <?php endforeach ?>
  </div>
<?php endif ?>