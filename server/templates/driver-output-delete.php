<?php $this->layout('drivers-template') ?>
<h2>Delete Driver Output</h2>
<div class="row">
  <div class="col-md-12">
    <form action="drivers.php?do=deleteoutput&amp;oid=<?=$this->e($driverOutput->getId())?>" method="post" class="form-horizontal">
      <input type="hidden" name="confirm" value="true"/>
      <p class="text-danger">Are you sure you wish to delete driver output "<?=$this->e($driverOutput->getName())?>"?</p>
      <div class="form-group">
        <div class="col-md-12">
          <button type="submit" class="btn btn-danger">Delete</button>
          <a class="btn btn-primary" href="drivers.php?do=listoutputs&amp;id=<?=$this->e($driverOutput->getDriverId())?>" role="button">Back</a>
        </div>
      </div>
    </form>
  </div>
</div>