<?php $this->layout('drivers-template') ?>
<h2>Edit Driver Output</h2>
<div class="row">
  <div class="col-md-4">
    <form action="drivers.php?do=editoutput&amp;oid=<?=$this->e($driverOutput->getId())?>" method="post" class="form-horizontal">
      <div class="form-group<?php if (array_key_exists('name', $errors)): ?> has-error<?php endif; ?>">
        <label for="name" class="control-label col-md-4">Name</label>
        <div class="col-md-8">
          <input class="form-control" type="text" name="name" id="name" placeholder="Name" maxlength="32" value="<?=$this->e($driverOutput->getName())?>"/>
          <?php if (array_key_exists('name', $errors)): ?>
          <?php foreach ($errors['name'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group<?php if (array_key_exists('mapping', $errors)): ?> has-error<?php endif; ?>">
        <label for="mapping" class="control-label col-md-4">Pin Mapping</label>
        <div class="col-md-3">
          <input class="form-control" type="text" name="mapping" id="mapping" placeholder="64" maxlength="3" value="<?=$this->e($driverOutput->getMapping())?>"/>
          <?php if (array_key_exists('mapping', $errors)): ?>
          <?php foreach ($errors['mapping'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group<?php if (array_key_exists('overlap_value', $errors)): ?> has-error<?php endif; ?>">
        <label for="overlapvalue" class="control-label col-md-4">Overlap Value</label>
        <div class="col-md-3">
          <input class="form-control" type="text" name="overlapvalue" id="overlapvalue" placeholder="127" maxlength="3" value="<?=$this->e($driverOutput->getOverlapValue())?>"/>
          <?php if (array_key_exists('overlap_value', $errors)): ?>
          <?php foreach ($errors['overlap_value'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group<?php if (array_key_exists('default_delay', $errors)): ?> has-error<?php endif; ?>">
        <label for="defaultdelay" class="control-label col-md-4">Default Delay</label>
        <div class="col-md-4">
          <div class="input-group">
            <input class="form-control" type="text" name="defaultdelay" id="defaultdelay" placeholder="5" maxlength="5" value="<?=$this->e($driverOutput->getDefaultDelay())?>"/>
            <div class="input-group-addon">ms</div>
          </div>
          <?php if (array_key_exists('default_delay', $errors)): ?>
          <?php foreach ($errors['default_delay'] as $error): ?>
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