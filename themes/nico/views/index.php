<?php
    $default_nico_theme_type = get_option('default_nico_type', 'light');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <!-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> -->
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="en" >
    <meta name="description" content="<?=get_option('website_desc', "SmartPanel - #1 SMM Reseller Panel - Best SMM Panel for Resellers. Also well known for TOP SMM Panel and Cheap SMM Panel for all kind of Social Media Marketing Services. SMM Panel for Facebook, Instagram, YouTube and more services!")?>">
    <meta name="keywords" content="<?=get_option('website_keywords', "smm panel, SmartPanel, smm reseller panel, smm provider panel, reseller panel, instagram panel, resellerpanel, social media reseller panel, smmpanel, panelsmm, smm, panel, socialmedia, instagram reseller panel")?>">
    <title><?=get_option('website_title', "SmartPanel - SMM Panel Reseller Tool")?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?=get_option('website_favicon', BASE."assets/images/favicon.png")?>">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <script src="<?php echo BASE; ?>assets/js/vendors/jquery-3.2.1.min.js"></script>
    <link href="<?php echo BASE; ?>themes/nico/assets/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo BASE; ?>themes/nico/assets/css/fontawesome-all.css" rel="stylesheet">
    <link href="<?php echo BASE; ?>themes/nico/assets/css/swiper.css" rel="stylesheet">
    <link href="<?php echo BASE; ?>assets/plugins/aos/dist/aos.css" rel="stylesheet">
    <link href="<?php echo BASE; ?>assets/plugins/lineicons/web-font-files/lineicons.css" rel="stylesheet">
	<link href="<?php echo BASE; ?>themes/nico/assets/css/styles.css" rel="stylesheet">
    <?php if ($default_nico_theme_type == 'dark') : ?>
	    <link href="<?php echo BASE; ?>themes/nico/assets/css/dark.css" rel="stylesheet">
    <?php endif; ?>
	<!-- Favicon  -->
    <link rel="icon" href="<?php echo BASE; ?>themes/nico/assets/images/favicon.png">
    <script type="text/javascript">
      var token = '<?=$this->security->get_csrf_hash()?>',
          PATH  = '<?php echo PATH; ?>',
          BASE  = '<?php echo BASE; ?>';
      var    deleteItem  = '<?php echo lang("Are_you_sure_you_want_to_delete_this_item"); ?>';
      var    deleteItems = '<?php echo lang("Are_you_sure_you_want_to_delete_all_items"); ?>';
    </script>
    <?php echo htmlspecialchars_decode(get_option('embed_head_javascript', ''), ENT_QUOTES); ?>
</head>
<body data-spy="scroll" data-target=".fixed-top">
    
    <!-- Preloader -->
	<div class="spinner-wrapper">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container">
            <?php
                $logo_area_html = '';
                if ($default_nico_theme_type == 'dark') {
                    $logo_url = get_option('website_logo_white', BASE."assets/images/logo_white.png");
                } else {
                    $logo_url = get_option('website_logo', BASE."assets/images/logo.png");
                }
                $logo_area_html = sprintf(
                    '<a class="navbar-brand logo-image" href="%s">
                        <img src="%s" alt="%s">
                    </a> ', cn(), $logo_url, get_option('website_name')
                );
                echo $logo_area_html;
            ?>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-awesome fas fa-bars"></span>
                <span class="navbar-toggler-awesome fas fa-times"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <?php echo render_header_nav_ul(); ?>
                <span class="nav-item">
                <?php 
                    if (!session('uid')) {
                    ?>
                    <a class="btn btn-login" href="<?=cn('auth/login')?>"><?=lang("Login")?></a>
                    <?php if (!get_option('disable_signup_page')) { ?>
                        <a class="btn btn-primary btn-getstarted" href="<?=cn('auth/signup')?>"><?=lang("Sign_Up")?></a>
                    <?php }; ?>
                    <?php } else {?>
                    <a class="btn btn-primary btn-getstarted" href="<?=cn('statistics')?>"><?=lang("dashboard")?></a>
                    <?php } ?>
                </span>
            </div>
        </div>
    </nav> 
    <!-- Header -->
    <header id="header" class="header">
        <div class="header-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-xl-6">
                        <div class="text-container">
                            <h1 data-aos="fade-up"><?php echo lang("the_best_social_media_panel_in_the_market"); ?></h1>
                            <p class="p-large" data-aos="fade-up" data-aos-delay="400"><?php echo lang("manage_all_social_media_networks_from_a_single_panel_quality_and_cheap_we_provide_services_on_todays_most_popular_social_networks_we_have_instagram_twitter_facebook_youtube_tiktok_spotify_and_many_more_services"); ?></p>
                            <a class="btn btn-primary btn-getstarted" href="signup.html" data-aos="fade-up" data-aos-delay="600"><?=lang("get_start_now")?> <i class="fas fa-arrow-right"></i> </a>
                        </div>
                    </div> 
                    <div class="col-lg-6 col-xl-6">
                        <div class="image-container" data-aos="zoom-out" data-aos-delay="200">
                            <div class="img-wrapper">
                                <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/home/header-hero.png" alt="header hero">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <?php
        $count_orders = landing_page_count_area('orders');
        $count_active_service = landing_page_count_area('services');
    ?>
    <!-- Count Area -->
    <section class="counts-area">
        <div class="container" data-aos="fade-up">
          <div class="row gy-4">
            <div class="col-lg-3 col-md-6">
              <div class="count-box">
                <i class="lni lni-users"></i>
                <div>
                  <span data-purecounter-start="0" data-purecounter-end="<?php echo get_option('default_happy_clients_number', rand(5239, 23989)); ?>" data-purecounter-duration="1" class="purecounter"></span>
                  <p><?php echo lang("happy_clients"); ?></p>
                </div>
              </div>
            </div>
    
            <div class="col-lg-3 col-md-6">
              <div class="count-box">
                <i class="lni lni-gift text-danger"></i>
                <div>
                  <span data-purecounter-start="0" data-purecounter-end="<?php echo $count_orders; ?>" data-purecounter-duration="1" class="purecounter"></span>
                  <p><?php echo lang("total_orders"); ?></p>
                </div>
              </div>
            </div>
    
            <div class="col-lg-3 col-md-6">
              <div class="count-box">
                <i class="lni lni-phone-set text-warning"></i>
                <div>
                  <span data-purecounter-start="0" data-purecounter-end="<?php echo get_option('default_hours_of_support_number', rand(13239, 23989)); ?>" data-purecounter-duration="1" class="purecounter"></span>
                  <p><?php echo lang("hours_of_support"); ?></p>
                </div>
              </div>
            </div>
    
            <div class="col-lg-3 col-md-6">
              <div class="count-box">
                <i class="lni lni-network text-success"></i>
                <div>
                  <span data-purecounter-start="0" data-purecounter-end="<?php echo $count_active_service; ?>" data-purecounter-duration="1" class="purecounter"></span>
                  <p><?php echo lang("fast_services"); ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
    </section>

    <!-- why-choose-us -->
    <section class="why-choose-us" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 section-header">
                    <div class="above-heading" data-aos="fade-up" data-aos-delay="200"><?php echo lang("why_choose_us"); ?></div>
                    <h2 class="h2-heading"  data-aos="fade-up" data-aos-delay="400"><?php echo lang("we_are_help_dominate_social_media_with_the_largest_social_media_panel"); ?></h2>
                </div> 
            </div>
            <div class="row">
                <div class="col-lg-3" data-aos="fade-up" data-aos-delay="600">
                  <div class="box">
                    <img src="<?php echo BASE; ?>themes/nico/assets/images/home/great-quality.png" class="img-fluid" alt="">
                    <h3><?php echo lang("great_quality"); ?></h3>
                    <p><?php echo lang("youll_be_satisfied_with_smm_services_we_provide"); ?></p>
                  </div>
                </div>
      
                <div class="col-lg-3 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="800">
                  <div class="box">
                    <img src="<?php echo BASE; ?>themes/nico/assets/images/home/payment-methods.png" class="img-fluid" alt="">
                    <h3><?php echo lang("many_payment_methods"); ?></h3>
                    <p><?php echo lang("enjoy_a_fantastic_selection_of_payment_methods_that_we_offer"); ?></p>
                  </div>
                </div>
      
                <div class="col-lg-3 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="1000">
                  <div class="box">
                    <img src="<?php echo BASE; ?>themes/nico/assets/images/home/shoking-prices.png" class="img-fluid" alt="">
                    <h3><?php echo lang("shoking_prices"); ?></h3>
                    <p><?php echo lang("you_will_be_satisfied_with_how_cheap_our_services_are"); ?></p>
                  </div>
                </div>
      
                <div class="col-lg-3 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="1200">
                  <div class="box">
                    <img src="<?php echo BASE; ?>themes/nico/assets/images/home/unbelievable-prices.png" class="img-fluid" alt="">
                    <h3><?php echo lang("unbelievable_prices"); ?></h3>
                    <p><?php echo lang("our_prices_most_reasonable_in_the_market_starting_from_at_0001"); ?></p>
                  </div>
                </div>
            </div>
        </div>
    </section>

    <!-- basic-1 -->
    <section class="basic-1">
        <div class="container" data-aos="fade-up">
            <div class="row">
                <div class="col-lg-6">
                    <div class="image-container" data-aos="zoom-out" data-aos-delay="200">
                        <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/home/who-we-are.png" alt="who we are">
                    </div>
                </div> 
                <div class="col-lg-6">
                    <div class="text-container" data-aos="fade-up" data-aos-delay="200">
                        <h4><?php echo lang("who_we_are"); ?></h4>
                        <h2><?php echo lang("what_we_offer_for_your_succes_brand"); ?></h2>
                        <p><?php echo lang("we_are_active_for_support_only_24_hours_a_day_and_seven_times_a_week_with_all_of_your_demands_and_services_around_the_day__dont_go_anywhere_else_we_are_here_ready_to_serve_you_and_help_you_with_all_of_your_smm_needs_users_or_clients_with_smm_orders_and_in_need_of_cheap_smm_services_are_more_then_welcome_in_our_smm_panel"); ?></p>
                        <a class="btn btn-primary btn-getstarted" href="sign-up.html">View Services <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- social-icons -->
    <section class="social-icons" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 section-header"  data-aos="fade-up" data-aos-delay="200">
                    <div class="above-heading"><?php echo lang("social_media_is_our_business"); ?></div>
                    <h2 class="h2-heading"><?php echo lang("providing_service_in_all_social_networks"); ?></h2>
                </div> 
            </div>
            <div class="row">
                <div class="col-lg-6 mt-5 mt-lg-0 d-flex">
                  <div class="row align-self-center gy-4">
                    <div class="col-md-6 icon" data-aos="zoom-out" data-aos-delay="200">
                      <div class="feature-box d-flex align-items-center">
                        <i class="lni lni-instagram-original"></i>
                        <h3>Instagram</h3>
                      </div>
                    </div>
                    <div class="col-md-6 icon" data-aos="zoom-out" data-aos-delay="400">
                      <div class="feature-box d-flex align-items-center">
                        <i class="lni lni-facebook-line"></i>
                        <h3>Facebook</h3>
                      </div>
                    </div>
                    <div class="col-md-6 icon" data-aos="zoom-out" data-aos-delay="600">
                      <div class="feature-box d-flex align-items-center">
                        <i class="lni lni-tiktok"></i>
                        <h3>TikTok</h3>
                      </div>
                    </div>
                    <div class="col-md-6 icon" data-aos="zoom-out" data-aos-delay="700">
                      <div class="feature-box d-flex align-items-center">
                        <i class="lni lni-youtube"></i>
                        <h3>Youtube</h3>
                      </div>
                    </div>
                    <div class="col-md-6 icon" data-aos="zoom-out" data-aos-delay="850">
                      <div class="feature-box d-flex align-items-center">
                        <i class="lni lni-twitter"></i>
                        <h3>Twitter</h3>
                      </div>
                    </div>
                    <div class="col-md-6 icon" data-aos="zoom-out" data-aos-delay="1050">
                      <div class="feature-box d-flex align-items-center">
                        <i class="lni lni-vmware"></i>
                        <h3><?php echo lang("other_services"); ?></h3>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6" data-aos="zoom-out" data-aos-delay="200">
                  <img src="<?php echo BASE; ?>themes/nico/assets/images/home/why-us.png" class="img-fluid" alt="why-choose-us">
                </div>
            </div> 
        </div>
    </section>

    <!-- feature -->
    <section class="feature">
        <div class="container" data-aos="fade-up">
            <div class="row">
                <div class="col-lg-12 section-header">
                    <div class="above-heading" data-aos="fade-up" data-aos-delay="200"><?php echo lang("What_we_offer"); ?></div>
                    <h2 class="h2-heading" data-aos="fade-up" data-aos-delay="400"><?php echo lang("we_are_help_dominate_social_media_with_the_largest_social_media_panel"); ?></h2>
                </div> 
            </div>
            <div class="row feature-icons" data-aos="fade-up">
                <div class="row">
                <div class="col-xl-4 text-center" data-aos="fade-right" data-aos-delay="100">
                    <img src="<?php echo BASE; ?>themes/nico/assets/images/home/what-we-offer.png" class="img-fluid p-4" alt="what we offer">
                </div>
                <div class="col-xl-8 d-flex content">
                    <div class="row align-self-center gy-4">
    
                    <div class="col-md-6 icon-box" data-aos="fade-up">
                        <div class="icon">
                            <i class="lni lni-consulting"></i>
                        </div>
                        <div>
                            <h4><?=lang("Resellers")?></h4>
                            <p><?=lang("you_can_resell_our_services_and_grow_your_profit_easily_resellers_are_important_part_of_smm_panel")?></p>
                        </div>
                    </div>
                    <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="100">
                        <div class="icon">
                            <i class="lni lni-phone-set"></i>
                        </div>
                        <div>
                            <h4><?=lang("Supports")?></h4>
                            <p><?=lang("technical_support_for_all_our_services_247_to_help_you")?></p>
                        </div>
                    </div>
                    <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="200">
                        <div class="icon">
                            <i class="lni lni-star-half"></i>
                        </div>
                        <div>
                            <h4><?=lang("high_quality_services")?></h4>
                            <p><?=lang("get_the_best_high_quality_services_and_in_less_time_here")?></p>
                        </div>
                    </div>
                    <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="300">
                        <div class="icon">
                            <i class="lni lni-checkmark-circle"></i>
                        </div>
                        <div>
                            <h4><?=lang("Updates")?></h4>
                            <p><?=lang("services_are_updated_daily_in_order_to_be_further_improved_and_to_provide_you_with_best_experience")?></p>
                        </div>
                    </div>
                    <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="400">
                        <div class="icon">
                            <i class="lni lni-unlink"></i>
                        </div>
                        <div>
                            <h4><?=lang("api_support")?></h4>
                            <p><?=lang("we_have_api_support_for_panel_owners_so_you_can_resell_our_services_easily")?></p>
                        </div>
                    </div>
                    <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="500">
                        <div class="icon">
                            <i class="lni lni-investment"></i>
                        </div>
                        <div>
                            <h4><?=lang("secure_payments")?></h4>
                            <p><?=lang("we_have_a_popular_methods_as_paypal_and_many_more_can_be_enabled_upon_request")?></p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </section>

    <!-- work-process-area -->
    <section class="work-process-area" data-aos="fade-up">
        <div class="container">
          <div class="row  justify-content-center" data-aos="fade-up" data-aos-delay="200">
            <div class="col-12 col-sm-8 col-lg-6">
              <header class="section-header">
                <h2><?php echo lang("how_it_works"); ?></h2>
                <p><?php echo lang("by_following_the_processes_below_you_can_make_any_order_you_want"); ?></p>
              </header>
            </div>  
          </div>
          <div class="row justify-content-between">
              <div class="col-12 col-sm-3 col-md-3" data-aos="fade-up" data-aos-delay="300">
                  <div class="single_work_step">
                      <div class="step-icon"><i>1</i></div>
                      <h5><?php echo lang("register_and_log_in"); ?></h5>
                      <p><?php echo lang("creating_an_account_is_the_first_step_then_you_need_to_log_in"); ?></p>
                  </div>
              </div>
              <div class="col-12 col-sm-3 col-md-3" data-aos="fade-up" data-aos-delay="400">
                  <div class="single_work_step">
                      <div class="step-icon"><i>2</i></div>
                      <h5><?php echo lang("add_funds"); ?></h5>
                      <p><?php echo lang("next_pick_a_payment_method_and_add_funds_to_your_account"); ?></p>
                  </div>
              </div>
              <div class="col-12 col-sm-3 col-md-3" data-aos="fade-up" data-aos-delay="500">
                  <div class="single_work_step">
                      <div class="step-icon"><i>3</i></div>
                      <h5><?php echo lang("select_a_service"); ?></h5>
                      <p><?php echo lang("select_the_services_you_want_and_get_ready_to_receive_more_publicity"); ?></p>
                  </div>
              </div>
              <div class="col-12 col-sm-3 col-md-3" data-aos="fade-up" data-aos-delay="600">
                  <div class="single_work_step">
                      <div class="step-icon"><i>4</i></div>
                      <h5><?php echo lang("enjoy_popularity"); ?></h5>
                      <p><?php echo lang("you_can_enjoy_incredible_results_when_your_order_is_complete"); ?></p>
                  </div>
              </div>
          </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faqs" data-aos="fade-up" data-aos-duration="2000">
        <div class="container  justify-content-center">
            <div class="row">
                <div class="col-lg-12 section-header">
                    <div class="above-heading">F.A.Q</div>
                    <h2 class="h2-heading"><?php echo lang("we_answered_some_of_the_most_frequently_asked_questions_on_our_panel"); ?></h2>
                </div> 
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="faq-block__card">
                                <div class="card">
                                    <div class="faq-block__header collapsed" data-toggle="collapse" data-target="#faq-block-10-1" aria-expanded="false" aria-controls="#faq-block-10-1">
                                        <div class="faq-block__header-title">
                                            <h4><i class="far fa-question-circle"></i> <?php echo lang("smm_panels__what_are_they"); ?></h4>
                                        </div>
                                        <div class="faq-block__header-icon">
                                            <i class="fas fa-chevron-circle-down fa-icon"></i>
                                        </div>
                                    </div>
                                    <div class="faq-block__body collapse" id="faq-block-10-1">
                                        <div class="faq-block__body-description">
                                            <p><?php echo lang("an_smm_panel_is_an_online_shop_that_you_can_visit_to_puchase_smm_services_at_great_prices"); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="faq-block__card">
                                <div class="card">
                                    <div class="faq-block__header collapsed" data-toggle="collapse" data-target="#faq-block-10-2" aria-expanded="false" aria-controls="#faq-block-10-2">
                                        <div class="faq-block__header-title">
                                            <h4><i class="far fa-question-circle"></i> <?php echo lang("what_smm_services_can_i_find_on_this_panel"); ?></h4>
                                        </div>
                                        <div class="faq-block__header-icon">
                                            <i class="fas fa-chevron-circle-down fa-icon"></i>
                                        </div>
                                    </div>
                                    <div class="faq-block__body collapse" id="faq-block-10-2">
                                        <div class="faq-block__body-description">
                                            <p><?php echo lang("we_sell_different_types_of_smm_services__likes_followers_views_etc"); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="faq-block__card">
                                <div class="card">
                                    <div class="faq-block__header collapsed" data-toggle="collapse" data-target="#faq-block-10-3" aria-expanded="false" aria-controls="#faq-block-10-3">
                                        <div class="faq-block__header-title">
                                            <h4><i class="far fa-question-circle"></i> <?php echo lang("are_smm_services_on_your_panel_safe_to_buy"); ?></h4>
                                        </div>
                                        <div class="faq-block__header-icon">
                                            <i class="fas fa-chevron-circle-down fa-icon"></i>
                                        </div>
                                    </div>
                                    <div class="faq-block__body collapse" id="faq-block-10-3">
                                        <div class="faq-block__body-description">
                                            <p><?php echo lang("sure_your_accounts_wont_get_banned"); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="faq-block__card">
                                <div class="card">
                                    <div class="faq-block__header collapsed" data-toggle="collapse" data-target="#faq-block-10-4" aria-expanded="false" aria-controls="#faq-block-10-4">
                                        <div class="faq-block__header-title">
                                            <h4><i class="far fa-question-circle"></i> <?php echo lang("how_does_a_mass_order_work"); ?></h4>
                                        </div>
                                        <div class="faq-block__header-icon">
                                            <i class="fas fa-chevron-circle-down fa-icon"></i>
                                        </div>
                                    </div>
                                    <div class="faq-block__body collapse" id="faq-block-10-4">
                                        <div class="faq-block__body-description">
                                            <p><?php echo lang("its_possible_to_place_multiple_orders_with_different_links_at_once_with_the_help_of_the_mass_order_feature"); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="faq-block__card">
                                <div class="card">
                                    <div class="faq-block__header collapsed" data-toggle="collapse" data-target="#faq-block-10-5" aria-expanded="false" aria-controls="#faq-block-10-5">
                                        <div class="faq-block__header-title">
                                            <h4><i class="far fa-question-circle"></i> <?php echo lang("what_does_dripfeed_mean"); ?></h4>
                                        </div>
                                        <div class="faq-block__header-icon">
                                            <i class="fas fa-chevron-circle-down fa-icon"></i>
                                        </div>
                                    </div>
                                    <div class="faq-block__body collapse" id="faq-block-10-5">
                                        <div class="faq-block__body-description">
                                            <p><?php echo lang("grow_your_accounts_as_fast_as_you_want_with_the_help_of_dripfeed_how_it_works_lets_say_you_want_2000_likes_on_your_post_instead_of_getting_all_2000_at_once_you_can_get_200_each_day_for_10_days"); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <div class="slider-2">
        <div class="container" data-aos="fade-up" data-aos-duration="2000">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-lg-8">
                    <header class="section-header">
                        <h2><?php echo lang("what_people_say_about_us"); ?></h2>
                        <p><?php echo lang("our_service_has_an_extensive_customer_roster_built_on_years_worth_of_trust_read_what_our_buyers_think_about_our_range_of_service"); ?></p>
                    </header>
                </div>  
            </div>
            <div class="row">
                <div class="col-lg-12">      
                    <div class="slider-container">
                        <div class="swiper-container text-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="image-wrapper">
                                        <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/testimonial-1.jpg" alt="alternative">
                                    </div>
                                    <div class="text-wrapper">
                                        <div class="testimonial-text"><?php echo lang("client_one_comment"); ?></div>
                                        <div class="testimonial-author"><?php echo lang('client_one'); ?> - <?php echo lang('client_one_jobname'); ?></div>
                                    </div> 
                                </div>
                                <div class="swiper-slide">
                                    <div class="image-wrapper">
                                        <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/testimonial-2.jpg" alt="alternative">
                                    </div>
                                    <div class="text-wrapper">
                                        <div class="testimonial-text"><?php echo lang("client_two_comment"); ?></div>
                                        <div class="testimonial-author"><?php echo lang('client_two'); ?> - <?php echo lang('client_two_jobname'); ?></div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="image-wrapper">
                                        <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/testimonial-3.jpg" alt="alternative">
                                    </div>
                                    <div class="text-wrapper">
                                        <div class="testimonial-text"><?php echo lang("client_three_comment"); ?></div>
                                        <div class="testimonial-author"><?php echo lang('client_three'); ?> - <?php echo lang('client_three_jobname'); ?></div>
                                    </div>
                                </div>
                            </div> 
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div> 

    <!-- Payments Method -->
    <section class="payment-methods" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 section-header" data-aos="fade-up"  data-aos-delay="200">
                    <div class="above-heading"><?php echo lang("payment_methods"); ?></div>
                </div> 
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="slider-container" data-aos="fade-up"  data-aos-delay="400">
                        <div class="swiper-container image-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/payments/doller-icon.svg" alt="Dolar">
                                </div>
                                <div class="swiper-slide">
                                    <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/payments/paypal-icon.svg" alt="paypal">
                                </div>
                                <div class="swiper-slide">
                                    <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/payments/mastercard-icon.svg" alt="mastercard">
                                </div>
                                <div class="swiper-slide">
                                    <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/payments/bitcoin-icon.svg" alt="bitcoin">
                                </div>
                                <div class="swiper-slide">
                                    <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/payments/litecoin-icon.svg" alt="litecoin">
                                </div>
                                <div class="swiper-slide">
                                    <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/payments/bth-icon-2.svg" alt="alternative">
                                </div>
                                <div class="swiper-slide">
                                    <img class="img-fluid" src="<?php echo BASE; ?>themes/nico/assets/images/payments/visa-icon.svg" alt="visa">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </section>

    <!-- footer -->
    <footer id="footer" class="footer">
        <div class="container">
          <div class="row footer-top">
            <div class="col-lg-6 col-md-12 footer-info">
                <a href="index.html" class="logo d-flex align-items-center">
                    <img src="<?=get_option('website_logo_white', BASE."assets/images/logo_white.png")?>" alt="<?php echo get_option('website_name'); ?>">
                </a>
                <?php
                    $redirect = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                ?>
                <?php 
                    if (!empty($languages)) {
                ?>
                    <select class="form-custom footer-lang-selector ajaxChangeLanguage" name="ids" data-url="<?=cn('set-language')?>" data-redirect="<?php echo $redirect; ?>">
                        <?php 
                            foreach ($languages as $key => $row) {
                        ?>
                        <option value="<?=$row->ids?>" <?=(!empty($lang_current) && $lang_current->code == $row->code) ? 'selected' : '' ?> ><?=language_codes($row->code)?></option>
                        <?php }?>
                    </select>
                <?php }?>
              <p><?php echo lang("all_user_information_is_kept_100_private_and_will_not_be_shared_with_anyone_always_remember_you_are_protected_with_our_panel__most_trusted_smm_panel"); ?></p>
              <div class="icon-container">
                <?php if (get_option('social_facebook_link')): ?>
                    <span class="fa-stack">
                        <a href="<?php echo get_option('social_facebook_link'); ?>">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fab fa-facebook-f fa-stack-1x"></i>
                        </a>
                    </span>
                <?php endif; ?>
                <?php if (get_option('social_twitter_link')): ?>
                    <span class="fa-stack">
                        <a href="<?php echo get_option('social_twitter_link'); ?>">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fab fa-twitter fa-stack-1x"></i>
                        </a>
                    </span>
                <?php endif; ?>
                <?php if (get_option('social_pinterest_link')): ?>
                    <span class="fa-stack">
                        <a href="<?php echo get_option('social_pinterest_link'); ?>">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fab fa-pinterest-p fa-stack-1x"></i>
                        </a>
                    </span>
                <?php endif; ?>
                <?php if (get_option('social_instagram_link')): ?>
                    <span class="fa-stack">
                        <a href="<?php echo get_option('social_instagram_link'); ?>">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fab fa-instagram fa-stack-1x"></i>
                        </a>
                    </span>
                <?php endif; ?>
                <?php if (get_option('social_youtube_link')): ?>
                    <span class="fa-stack">
                        <a href="<?php echo get_option('social_youtube_link'); ?>">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fab fa-youtube fa-stack-1x"></i>
                        </a>
                    </span>
                <?php endif; ?>
            </div>
            </div>
            <div class="col-lg-6 footer-newsletter">
              <div class="row justify-content-center">
                <div class="col-lg-12 text-center">
                  <h4><?php echo lang("newsletter"); ?></h4>
                  <p><?php echo lang("fill_in_the_ridiculously_small_form_below_to_receive_our_ridiculously_cool_newsletter"); ?></p>
                </div>
                <div class="col-lg-12">
                    <form id="newsletterForm" class="actionFormWithoutToast" action="<?php echo cn("client/subscriber"); ?>"  method="POST">
                        <div class="subscribe-box">
                            <input type="email" name="email" placeholder="<?php echo lang("email"); ?>" required>
                            <input type="submit" class="btn-submit" value="<?php echo lang("subscribe_now"); ?>"> 
                        </div>
                        <div id="alert-message" class="alert-message-reponse"></div>
                    </form>
                </div>
              </div>
            </div>
          </div>
          <div class="wrapper footer-bottom">
            <div class="copyrights">
              <p><?=get_option('copy_right_content', "Copyright &copy; 2023 - SmartPanel");?></p>
            </div>
            <div class="footer-menu footer-links">
              <ul>
                <li><a href="<?= cn('services'); ?>"><?php echo lang("Services"); ?></a></li>
                <li><a href="<?= cn('faq'); ?>"><?php echo lang("FAQs"); ?></a></li>
                <li><a href="<?= cn('api/docs'); ?>"><?php echo lang("API"); ?></a></li>
                <li><a href="<?= cn('blog'); ?>"><?php echo lang("Blog"); ?></a></li>
                <li><a href="<?= cn('terms'); ?>"><?php echo lang("terms__conditions"); ?></a></li>
              </ul>
            </div>
          </div>
        </div>
    </footer>
    
    <div class="modal-infor">
      <div class="modal" id="notification">
        <div class="modal-dialog">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title"><i class="fe fe-bell"></i> <?=lang("Notification")?></h4>
              <button type="button" class="close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <?=get_option('notification_popup_content')?>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal"><?=lang("Close")?></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Vendor -->
    <script src="<?php echo BASE; ?>assets/plugins/purecounter/purecounter_vanilla.js"></script>
    <script src="<?php echo BASE; ?>assets/plugins/aos/dist/aos.js"></script>
    <script src="<?php echo BASE; ?>themes/nico/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE; ?>themes/nico/assets/js/jquery.easing.min.js"></script> 
    <script src="<?php echo BASE; ?>themes/nico/assets/js/swiper.min.js"></script>
    <script src="<?php echo BASE; ?>themes/nico/assets/js/scripts.js"></script>

    <!-- Script js -->
    <script src="<?php echo BASE; ?>assets/js/process.js"></script>
    <script src="<?php echo BASE; ?>assets/js/general.js"></script>
    <?=htmlspecialchars_decode(get_option('embed_javascript', ''), ENT_QUOTES)?>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        /**
         * Initiate Pure Counter 
         */
        new PureCounter();

        /**
         * Animation on scroll
         */
        function aos_init() {
            AOS.init({
            duration: 1000,
            easing: "ease-in-out",
            once: true,
            mirror: false
            });
        }
        window.addEventListener('load', () => {
            aos_init();
        });
        
    </script>
    <script>
        $(document).ready(function(){
            var is_notification_popup = "<?=get_option('enable_notification_popup', 0)?>"
            setTimeout(function(){
                if (is_notification_popup == 1) {
                $("#notification").modal('show');
                }else{
                $("#notification").modal('hide');
                }
            },500);
        });
    </script>
    
</body>
</html>