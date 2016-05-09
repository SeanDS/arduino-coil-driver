<?php $this->layout('drivers-template') ?>
<h2>Delete Driver</h2>
<div class="row">
  <div class="col-md-12">
    <form action="drivers.php?do=delete&amp;id=<?=$this->e($driver->getId())?>" method="post" class="form-horizontal">
      <input type="hidden" name="confirm" value="true"/>
      <p class="text-danger">Are you sure you wish to delete driver "<?=$this->e($driver->getName())?>"?</p>
      <div class="form-group">
        <div class="col-md-12">
          <button type="submit" class="btn btn-danger">Delete</button>
          <a class="btn btn-primary" href="drivers.php" role="button">Back</a>
        </div>
      </div>
    </form>
  </div>
</div>