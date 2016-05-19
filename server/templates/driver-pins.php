<?php $this->layout('drivers-template') ?>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <?php $this->insert('driver-pins-list', ['driver' => $driver]) ?>
  </div>
</div>