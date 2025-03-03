<!DOCTYPE html>
<html lang="en">
  <?php 
    include_once 'blocks/head.blade.php';
    $cookie_email = '';
    $cookie_pass = '';
    if (isset($_COOKIE["cookie_email"])) {
      $cookie_email = encrypt_decode($_COOKIE["cookie_email"]);
    }
    if (isset($_COOKIE["cookie_pass"])) {
      $cookie_pass = encrypt_decode($_COOKIE["cookie_pass"]);
    }
    $form_url        = cn("auth/ajax_sign_in");
    $form_attributes = [
      'id'            => 'signUpForm', 
      'data-focus'    => 'false', 
      'class'         => 'actionFormWithoutToast', 
      'data-redirect' => cn('home'), 
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
                <p class="login-card-description"><?=lang("login_to_your_account")?></p>
                <?php echo form_open($form_url, $form_attributes); ?>
                  <div class="form-group">
                    <label for="email" class="sr-only"><?php echo lang("Email"); ?></label>
                    <input type="email" name="email" id="email" class="form-control" value="<?=(isset($cookie_email) && $cookie_email != "") ? $cookie_email : '' ?>" placeholder="<?php echo lang("Email"); ?>">
                  </div>
                  <div class="form-group">
                    <label for="password" class="sr-only"><?php echo lang("Password"); ?></label>
                    <input type="password" name="password" id="password" class="form-control" value="<?=(isset($cookie_pass) && $cookie_pass != "") ? $cookie_pass : ""?>" placeholder="<?php echo lang("Password"); ?>">
                  </div>

                  <div class="form-group mt-20">
                    <div id="alert-message" class="alert-message-reponse"></div>
                  </div>

                  <?php if (!session('uid')) : ?>
                    <div class="form-prompt-wrapper mb-4">
                      <div class="custom-control custom-checkbox login-card-check-box">
                        <input type="checkbox" class="custom-control-input" id="customCheck1" name="remember" <?=(isset($cookie_email) && $cookie_email != "") ? "checked" : ""?>>
                        <label class="custom-control-label" for="customCheck1"><?=lang("remember_me")?></label>
                      </div>              
                    </div>
                  <?php endif; ?> 

                  <button class="btn btn-block login-btn btn-submit mb-4" type="submit"><?=lang("Login")?></button>
                <?php echo form_close(); ?>

                <?php if (!session('uid')) : ?>
                  <p class="login-card-footer-text"><?=lang("dont_have_account_yet")?> <a href="<?=cn('auth/signup')?>" class="text-reset"><?=lang("Sign_Up")?></a></p>
                <?php endif; ?> 

                <?php if (!session('uid')) : ?>
                  <nav class="login-card-footer-nav">
                    <a href="<?=cn("auth/forgot_password")?>" class="text-reset"><?=lang("forgot_password")?>?</a>
                  </nav>
                <?php endif; ?>
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