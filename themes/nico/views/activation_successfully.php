<!DOCTYPE html>
<html lang="en">
  <?php include_once 'blocks/head.blade.php'; ?>
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
                <p class="login-card-description"><?=lang("congratulations_your_registration_is_now_complete")?></p>
                <p><?=lang('congratulations_desc')?></p>
                <div class="form-group">
                  <a class="btn btn-block login-btn mb-4" href="<?=cn('auth/login')?>" type="submit"><?=lang("get_start_now")?></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </body>
  <?php include_once 'blocks/script.blade.php'; ?>
</html>
