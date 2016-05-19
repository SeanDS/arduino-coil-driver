<?php $this->layout('drivers-template') ?>
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <?php $this->insert('drivers-unregistered-list', ['drivers' => $drivers]) ?>
  </div>
</div>