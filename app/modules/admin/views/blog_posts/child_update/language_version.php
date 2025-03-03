<div class="card">
    <div class="card-header">
        <h3 class="card-title">Language version</h3>
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
                        <ul>
                            <?php foreach ($items_lang as $key => $item_lang) : ?>
                                <?php 
                                    $link = admin_url($controller_name . '/update/'. $post_id .'?ref_lang='.$item_lang['code']);
                                    $flag_country = 'flag-icon-' . $item_lang['code'];
                                    echo sprintf('<li>
                                    <a href="%s" target="_blank"><span class="flag-icon flag-icon-%s"></span> %s <span class="fe fe-external-link"></span></a>
                                </li>', $link, strtolower($item_lang['country_code']), language_codes($item_lang['code']));
                                ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>