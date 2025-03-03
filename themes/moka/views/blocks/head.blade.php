<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fastkart">
    <meta name="keywords" content="Fastkart">
    <meta name="author" content="Fastkart">
    <link rel="icon" href=".<?= BASE ?>themes/moka/assets/images/favicon/1.png" type="image/x-icon">
    <title>On-demand last-mile delivery</title>

    <!-- Google font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">

    <!-- bootstrap css -->
    <link id="rtl-link" rel="stylesheet" type="text/css" href="<?= BASE ?>themes/moka/assets/css/vendors/bootstrap.css">

    <!-- wow css -->
    <link rel="stylesheet" href="<?= BASE ?>themes/moka/assets/css/animate.min.css">

    <!-- Iconly css -->
    <link rel="stylesheet" type="text/css" href="<?= BASE ?>themes/moka/assets/css/bulk-style.css">

    <!-- Template css -->
    <link id="color-link" rel="stylesheet" type="text/css" href="<?= BASE ?>themes/moka/assets/css/style.css">
            <script type="text/javascript">
            var token = '<?= $this->security->get_csrf_hash() ?>',
                    PATH = '<?= PATH ?>',
                    BASE = '<?= BASE ?>';
            var deleteItem = '<?= lang("Are_you_sure_you_want_to_delete_this_item") ?>';
            var deleteItems = '<?= lang("Are_you_sure_you_want_to_delete_all_items") ?>';
        </script>
        <?= htmlspecialchars_decode(get_option('embed_head_javascript', ''), ENT_QUOTES) ?>
</head>

<body>

    <!-- Loader Start -->
    <div class="fullpage-loader">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
    <!-- Loader End -->