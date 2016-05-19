<?php $this->layout('drivers-template') ?>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <?php $this->insert('driver-outputs-list', ['driver' => $driver, 'messageId' => $messageId]) ?>
  </div>
</div>