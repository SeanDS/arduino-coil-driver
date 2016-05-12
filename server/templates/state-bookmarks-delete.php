<?php $this->layout('states-template') ?>
<h2>Delete State Bookmark</h2>
<div class="row">
  <div class="col-md-12">
    <form action="states.php?do=deletebookmark&amp;id=<?=$this->e($bookmark->getId())?>" method="post" class="form-horizontal">
      <input type="hidden" name="confirm" value="true"/>
      <p class="text-danger">Are you sure you wish to delete state bookmark id <?=$this->e($bookmark->getId())?> (<?=$this->e(formatDate($bookmark->getState()->getTime()))?>)?</p>
      <p>This will not delete the associated state, only the bookmark.</p>
      <div class="form-group">
        <div class="col-md-12">
          <button type="submit" class="btn btn-danger">Delete</button>
          <a class="btn btn-primary" href="states.php" role="button">Back</a>
        </div>
      </div>
    </form>
  </div>
</div>