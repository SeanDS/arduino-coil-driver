<?php $this->layout('drivers-template') ?>
<h2>New Output for <?=$this->e($driver->getName())?></h2>
<div class="row">
  <div class="col-md-4">
    <?php if (count($driverPins) > 1): ?>
    <form action="drivers.php?do=newoutput&amp;id=<?=$this->e($driver->getId())?>" method="post" class="form-horizontal">
      <div class="form-group<?php if (array_key_exists('name', $errors)): ?> has-error<?php endif; ?>">
        <label for="name" class="control-label col-md-4">Name</label>
        <div class="col-md-8">
          <input class="form-control" type="text" name="name" id="name" placeholder="Name" maxlength="32"/>
          <?php if (array_key_exists('name', $errors)): ?>
          <?php foreach ($errors['name'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group<?php if (array_key_exists('coarsepinid', $errors)): ?> has-error<?php endif; ?>">
        <label for="coarsepinid" class="control-label col-md-4">Coarse Pin</label>
        <div class="col-md-8">
          <select id="coarsepinid" name="coarsepinid">
            <?php foreach ($driverPins as $driverPin): ?>
            <option value="<?=$this->e($driverPin->getId())?>"><?=$this->e($driverPin->getName())?></option>
            <?php endforeach; ?>
          </select>
          <?php if (array_key_exists('coarsepinid', $errors)): ?>
          <?php foreach ($errors['coarsepinid'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group<?php if (array_key_exists('finepinid', $errors)): ?> has-error<?php endif; ?>">
        <label for="finepinid" class="control-label col-md-4">Fine Pin</label>
        <div class="col-md-8">
          <select id="finepinid" name="finepinid">
            <?php foreach ($driverPins as $driverPin): ?>
            <option value="<?=$this->e($driverPin->getId())?>"><?=$this->e($driverPin->getName())?></option>
            <?php endforeach; ?>
          </select>
          <?php if (array_key_exists('finepinid', $errors)): ?>
          <?php foreach ($errors['finepinid'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="reset" class="btn btn-danger">Reset</button>
        </div>
      </div>
    </form>
    <?php else: ?>
      <p class="text-warning">There are no pairs of pins available to produce a new output.</p>
      <a class="btn btn-default" href="drivers.php?do=listoutputs&amp;id=<?=$this->e($driver->getId())?>" role="button">Back</a>
    <?php endif; ?>
  </div>
</div>