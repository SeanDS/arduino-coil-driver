<?php $this->layout('drivers-template') ?>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <?php $this->insert('drivers-list', ['drivers' => $drivers, 'messageId' => $messageId]) ?>
    <a class="btn btn-default" href="drivers.php?do=listunregistered" role="button">List Unregistered</a>
  </div>
</div>