<?php $this->layout('groups-template') ?>
<h2>Delete Group</h2>
<div class="row">
  <div class="col-md-4">
    <form action="groups.php?do=delete&amp;id=<?=$this->e($group->getId())?>" method="post" class="form-horizontal">
      <input type="hidden" name="confirm" value="true"/>
      <p class="text-danger">Are you sure you wish to delete group "<?=$this->e($group->getName())?>"?</p>
      <div class="form-group">
        <div class="col-md-12">
          <button type="submit" class="btn btn-danger">Delete</button>
          <a class="btn btn-primary" href="groups.php" role="button">Back</a>
        </div>
      </div>
    </form>
  </div>
</div>