<div class="row api-documentation"> 
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Cronjobs configure</h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
          <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
        </div>
      </div>
      <div class="card-body">
        <div class="note">
          To be able to send order, order status. Cron task must be configured on your hosting. In cpanel, you should click Cron task and fill this command at the command filed:
        </div>
        <h6 class="m-t-30">Cronjob for main script <span class="small text-danger">(Once/minute)</span></h6>
        <div>
          <?php foreach ($cron_links as $key => $item_link): ?>
            <div class="item m-t-20">
              <code><?=esc(sprintf($link_format, $item_link)); ?></code>
            </div>
          <?php endforeach ?>
        </div>
        <div class="m-t-30">
          <p> With <strong>Hostinger</strong>, Please click  <a href="https://bit.ly/3ygoAmH" target="_blank" rel="Documentation">this link</a> for more details</p>
        </div>
      </div>
    </div>
  </div>
</div>