
<style>
  .tickets-number{
    font-size: 14px !important;
  }
</style>

<?php
  $CI = &get_instance();
  $CI->load->model('model', 'model');
  $total_unread_tickets = $CI->model->count_results('id', TICKETS, ['admin_read' => 1]);
?>
<?php
  $sidebar_elements = app_config('controller')['admin'];
  $sidebar_elements = limit_controllers($sidebar_elements);
  $xhtml = '<ul class="navbar-nav mb-md-4" id="menu">';
  foreach ($sidebar_elements as $key => $item) {
    if ($item['area_title']) {
      $xhtml .= sprintf('<h6 class="navbar-heading first"><span class="text">%s</span></h6>', $item['name']);
    } else {
      // permission
      $permission_controller = $key;
      if (in_array($key, ['blacklist_ip', 'blacklist_email', 'blacklist_link'])) {
        $permission_controller = 'blacklist';
      }
      if (!staff_has_permission($permission_controller, 'index')) continue;

      $class_active = (admin_url($item['route-name']) == current_url()) ? 'active' : '';
      $xmtml_ticket_unread_numbers = null;
      if ($key == 'tickets') {
        $xmtml_ticket_unread_numbers = sprintf('<span class="ml-auto badge badge-warning">%s</span>', $total_unread_tickets);
      }
      $xhtml .= sprintf(
        '<li class="nav-item">
          <a class="nav-link %s" href="%s" data-toggle="tooltip" data-placement="right" title="%s">
            <span class="nav-icon">
              <i class="%s"></i>
            </span>
            <span class="nav-text">
              %s
              %s
            </span>
          </a>
        </li>', $class_active, admin_url($item['route-name']), $item['name'], $item['icon'],  $item['name'], $xmtml_ticket_unread_numbers);
    }
  }
  $xhtml .= '</ul>';
?>
<aside class="navbar navbar-side navbar-fixed js-sidebar" id="aside">
  <div class="mobile-logo">
    <a href="<?php echo admin_url('statistics'); ?>" class="navbar-brand text-inherit">
      <img src="<?=get_option('website_logo', BASE."assets/images/logo.png")?>" alt="Website Logo" class="hide-navbar-folded navbar-brand-logo">
      <img src="<?=get_option('website_logo_mark', BASE."assets/images/logo-mark.png")?>" alt="Website Logo" class="hide-navbar-expanded navbar-brand-logo">
    </a>
  </div>
  <div class="flex-fill scroll-bar">
    <?=$xhtml?>
  </div>
  <ul class="navbar-nav">
    <li class="nav-item">
      <a href="<?php echo admin_url('logout'); ?>" class="nav-link" data-toggle="tooltip" data-placement="right" title="Logout">
        <span class="nav-icon"><i class="icon fe fe-power"></i>
        </span>
        <span class="nav-text">Logout</span>
      </a>
    </li>
  </ul>
</aside>