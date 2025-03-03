<!DOCTYPE html>
<html lang="en">
  <?php 
    include_once 'blocks/head.blade.php';
    $form_url        = cn("auth/ajax_forgot_password");
    $form_attributes = [
      'id'            => 'signUpForm', 
      'data-focus'    => 'false', 
      'class'         => 'actionFormWithoutToast', 
      'data-redirect' => cn('auth/login'), 
      'method'        => "POST"  
    ];
  ?>
  <body>
    <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
      <div class="container">
        <div class="card login-card">
          <div class="row no-gutters">
            <div class="col-md-6 left-image mx-auto">
              <a href="<?=cn();?>"><img src="<?php echo BASE; ?>themes/nico/assets/images/login.png" alt="login" class="login-card-img"></a>
            </div>
            <div class="col-md-6">
              <div class="card-body">
                <div class="brand-wrapper">
                  <a href="<?=cn();?>"><img src="<?=get_option('website_logo', BASE."assets/images/logo.png")?>" alt="logo" class="logo"></a>
                </div>
                <p class="login-card-description"><?=lang("forgot_password")?></p>
                <?php echo form_open($form_url, $form_attributes); ?>
                  <p class=""><?=lang("enter_your_registration_email_address_to_receive_password_reset_instructions")?></p>
                  <div class="form-group">
                    <input type="email" name="email" id="email" class="form-control" placeholder="<?php echo lang("Email"); ?>">
                  </div>
                  <!-- reCAPCHA -->
                  <?php if (get_option('enable_goolge_recapcha') &&  get_option('google_capcha_site_key') != "" && get_option('google_capcha_secret_key') != "") : ?>
                    <div class="form-group">
                      <div class="g-recaptcha" data-sitekey="<?=get_option('google_capcha_site_key')?>"></div>
                    </div>
                  <?php endif; ?>
                  <div class="form-group mt-20">
                    <div id="alert-message" class="alert-message-reponse"></div>
                  </div>
                  <button class="btn btn-block login-btn btn-submit mb-4" type="submit"><?=lang("Submit")?></button>
                <?php echo form_close(); ?>

                <p class="login-card-footer-text"><?=lang("already_have_account")?> <a href="<?=cn('auth/login')?>"><?=lang("Login")?></a></p>
                <nav class="login-card-footer-nav">
                  <a href="<?=cn();?>" class="text-reset"><?=lang('back_to_home');?></a>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </body>
  <?php 
    include_once 'blocks/script.blade.php';
  ?>
</html>
