<?php $this->layout('states-template') ?>
<h2>Delete State</h2>
<div class="row">
  <div class="col-md-12">
    <form action="states.php?do=delete&amp;id=<?=$this->e($state->getId())?>" method="post" class="form-horizontal">
      <input type="hidden" name="confirm" value="true"/>
      <p class="text-danger">Are you sure you wish to delete state id <?=$this->e($state->getId())?> (<?=$this->e(formatDate($state->getTime()))?>)?</p>
      <?php if ($state->getStateBookmark() != null): ?>
      <p class="text-danger">This will delete the associated bookmarked state too.</p>
      <?php endif; ?>
      <div class="form-group">
        <div class="col-md-12">
          <button type="submit" class="btn btn-danger">Delete</button>
          <a class="btn btn-primary" href="states.php" role="button">Back</a>
        </div>
      </div>
    </form>
  </div>
</div>