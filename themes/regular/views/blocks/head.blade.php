<!doctype html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <!-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> -->
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta http-equiv="Content-Language" content="en" >
        <meta name="description" content="<?= get_option('website_desc', "SmartPanel - #1 SMM Reseller Panel - Best SMM Panel for Resellers. Also well known for TOP SMM Panel and Cheap SMM Panel for all kind of Social Media Marketing Services. SMM Panel for Facebook, Instagram, YouTube and more services!") ?>">
        <meta name="keywords" content="<?= get_option('website_keywords', "smm panel, SmartPanel, smm reseller panel, smm provider panel, reseller panel, instagram panel, resellerpanel, social media reseller panel, smmpanel, panelsmm, smm, panel, socialmedia, instagram reseller panel") ?>">
        <title><?= get_option('website_title', "SmartPanel - SMM Panel Reseller Tool") ?></title>

        <link rel="shortcut icon" type="image/x-icon" href="<?= get_option('website_favicon', BASE . "assets/images/favicon.png") ?>">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">


        <link rel="stylesheet" href="<?= BASE ?>assets/plugins/font-awesome/css/all.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,500,500i,600,600i,700,700i&amp;subset=latin-ext">
        <script src="<?= BASE ?>assets/js/vendors/jquery-3.2.1.min.js"></script>

        <!-- Core -->
        <link href="<?= BASE ?>assets/css/core.css" rel="stylesheet">

        <!-- toast -->
        <link rel="stylesheet" type="text/css" href="<?= BASE ?>assets/plugins/jquery-toast/css/jquery.toast.css">
        <link rel="stylesheet" href="<?= BASE ?>assets/plugins/boostrap/colors.css" id="theme-stylesheet">
        <link href="<?= BASE ?>assets/css/util.css" rel="stylesheet">
        <link href="<?= BASE ?>themes/regular/assets/css/theme_style.css" rel="stylesheet">
        <!-- AOS -->
        <link rel="stylesheet" href="<?php echo BASE; ?>assets/plugins/aos/dist/aos.css" />
        <link href="<?= BASE ?>assets/css/footer.css" rel="stylesheet">

        <script type="text/javascript">
            var token = '<?= $this->security->get_csrf_hash() ?>',
                    PATH = '<?= PATH ?>',
                    BASE = '<?= BASE ?>';
            var deleteItem = '<?= lang("Are_you_sure_you_want_to_delete_this_item") ?>';
            var deleteItems = '<?= lang("Are_you_sure_you_want_to_delete_all_items") ?>';
        </script>
        <?= htmlspecialchars_decode(get_option('embed_head_javascript', ''), ENT_QUOTES) ?>
    </head>
    <body class="">

        <div id="page-overlay" class="visible incoming">
            <div class="loader-wrapper-outer">
                <div class="loader-wrapper-inner">
                    <div class="lds-double-ring">
                        <div></div>
                        <div></div>
                        <div>
                            <div></div>
                        </div>
                        <div>
                            <div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
