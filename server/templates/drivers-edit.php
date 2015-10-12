<?php $this->layout('drivers-template') ?>
<h2>Edit Driver</h2>
<div class="row">
  <div class="col-md-4">
    <form action="drivers.php?do=edit&amp;id=<?=$this->e($driver->getId())?>" method="post" class="form-horizontal">
      <div class="form-group<?php if (array_key_exists('name', $errors)): ?> has-error<?php endif; ?>">
        <label for="name" class="control-label col-md-2">Name</label>
        <div class="col-md-10">
          <input class="form-control" type="text" name="name" id="name" placeholder="Name" maxlength="32" value="<?=$this->e($driver->getName())?>"/>
          <?php if (array_key_exists('name', $errors)): ?>
          <?php foreach ($errors['name'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="reset" class="btn btn-danger">Reset</button>
        </div>
      </div>
    </form>
  </div>
</div>