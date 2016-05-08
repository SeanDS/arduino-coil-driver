<?php $this->layout('groups-template') ?>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <?php $this->insert('groups-list', ['groups' => $groups, 'messageId' => $messageId]) ?>
    <a class="btn btn-default" href="groups.php?do=new" role="button">New</a>
  </div>
</div>