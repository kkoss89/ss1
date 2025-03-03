<!DOCTYPE html>
<html lang="en">
  <?php 
    include_once 'blocks/head.blade.php';
    $form_url        = cn("auth/ajax_reset_password/" . $reset_key);
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
              <img src="<?php echo BASE; ?>themes/nico/assets/images/login.png" alt="login" class="login-card-img">
            </div>
            <div class="col-md-6">
              <div class="card-body">
                <div class="brand-wrapper">
                  <a href="#"><img src="<?=get_option('website_logo', BASE."assets/images/logo.png")?>" alt="logo" class="logo"></a>
                </div>
                <p class="login-card-description"><?php echo lang("reset_your_password"); ?></p>
                <?php echo form_open($form_url, $form_attributes); ?>
                  <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="<?php echo lang("new_password"); ?>">
                  </div>
                  <div class="form-group">
                    <input type="password" name="re_password" id="re_password" class="form-control" placeholder="<?php echo lang("Confirm_password"); ?>">
                  </div>
                  <div class="form-group mt-20">
                    <div id="alert-message" class="alert-message-reponse"></div>
                  </div>
                  <button class="btn btn-block login-btn btn-submit mb-4" type="submit"><?=lang("Submit")?></button>
                <?php echo form_close(); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </body>
  <?php include_once 'blocks/script.blade.php'; ?>
</html>
