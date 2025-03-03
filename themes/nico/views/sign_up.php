<!DOCTYPE html>
<html lang="en">
  <?php 
    include_once 'blocks/head.blade.php';
    $form_url        = cn("auth/ajax_sign_up");
    $form_attributes = [
      'id'            => 'signUpForm', 
      'data-focus'    => 'false', 
      'class'         => 'actionFormWithoutToast', 
      'data-redirect' => cn('new_order'), 
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
                <p class="login-card-description"><?=lang("register_now")?></p>
                <?php echo form_open($form_url, $form_attributes); ?>
                  <div class="form-group">
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="<?php echo lang("first_name"); ?>">
                  </div>
                  <div class="form-group">
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="<?php echo lang("last_name"); ?>">
                  </div>
                  <div class="form-group">
                    <input type="email" name="email" id="email" class="form-control" placeholder="<?php echo lang("Email"); ?>">
                  </div>

                  <?php if (get_option('enable_signup_skype_field')) : ?>
                    <div class="form-group">
                      <input type="text" name="skype_id" id="skype_id" class="form-control" placeholder="<?php echo lang("Skype_id"); ?>">
                    </div>
                  <?php endif; ?>

                  <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="<?php echo lang("Password"); ?>">
                  </div>
                  <div class="form-group">
                    <input type="password" name="re_password" id="re_password" class="form-control" placeholder="<?php echo lang("Confirm_password"); ?>">
                  </div>
                  <div class="form-group">
                    <select  name="timezone" class="form-control square">
                      <?php $time_zones = tz_list(); ?>
                      <?php if (!empty($time_zones)) :
                          $location = get_location_info_by_ip(get_client_ip());
                          $user_timezone = $location->timezone;
                          if ($user_timezone == "" || $user_timezone == 'Unknow') {
                            $user_timezone = get_option("default_timezone", 'UTC');
                          }
                          foreach ($time_zones as $key => $time_zone) :
                      ?>
                        <option value="<?=$time_zone['zone']?>" <?=($user_timezone == $time_zone["zone"])? 'selected': ''?>><?=$time_zone['time']?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div>
                  <!-- reCAPCHA -->
                  <?php if (get_option('enable_goolge_recapcha') &&  get_option('google_capcha_site_key') != "" && get_option('google_capcha_secret_key') != "") : ?>
                    <div class="form-group">
                      <div class="g-recaptcha" data-sitekey="<?=get_option('google_capcha_site_key')?>"></div>
                    </div>
                  <?php endif; ?>
                  <!-- alert Message -->
                  <div class="form-group mt-20">
                    <div id="alert-message" class="alert-message-reponse"></div>
                  </div>
                  <div class="form-prompt-wrapper mb-4">
                    <div class="custom-control custom-checkbox login-card-check-box">
                      <input type="checkbox" class="custom-control-input" id="customCheck1" name="terms">
                      <label class="custom-control-label" for="customCheck1"><?=lang("i_agree_the")?> <a href="<?=cn('terms')?>"><?=lang("terms__policy")?></a></label>
                    </div>              
                  </div>
                  <button class="btn btn-block login-btn btn-submit mb-4" type="submit"><?=lang("create_new_account")?></button>
                <?php echo form_close(); ?>
                <p class="login-card-footer-text"><?=lang("already_have_account")?> <a href="<?=cn('auth/login')?>"><?=lang("Login")?></a></p>
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
