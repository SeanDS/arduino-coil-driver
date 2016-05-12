<?php $this->layout('states-template') ?>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <?php $this->insert('states-list', ['currentState' => $currentState, 'bookmarksPager' => $bookmarksPager, 'statesPager' => $statesPager, 'messageId' => $messageId]) ?>
  </div>
</div>