
<?php if (!empty($count_items_by_category)) : ?>
  <div class="widget">
    <h3 class="title"><?=lang('Categories')?></h3>
    <div class="widget-category">
      <?php foreach ($count_items_by_category as $key => $items) : ?>
        <?php
          $item_link_related_category = cn('blog/category/' . $key);
          $item_category_name = show_category_name_by_lang_code($items[0], $lang_code);
        ?>
          <a href="<?= $item_link_related_category; ?>"><?= $item_category_name; ?> <span>(<?=count($items); ?>)</span>
          </a>
        <?php endforeach ?>
    </div>
  </div>
<?php endif ?>