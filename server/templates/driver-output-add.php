<?php $this->layout('drivers-template') ?>
<h2>New Output for <?=$this->e($driver->getName())?></h2>
<div class="row">
  <div class="col-md-4">
    <?php if ($driverPins->count() > 1): ?>
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
      <div class="form-group<?php if (array_key_exists('coarse_pin_id', $errors)): ?> has-error<?php endif; ?>">
        <label for="coarsepinid" class="control-label col-md-4">Coarse Pin</label>
        <div class="col-md-8">
          <select id="coarsepinid" name="coarsepinid">
            <?php foreach ($driverPins as $driverPin): ?>
            <option value="<?=$this->e($driverPin->getId())?>"><?=$this->e($driverPin->getName())?></option>
            <?php endforeach; ?>
          </select>
          <?php if (array_key_exists('coarse_pin_id', $errors)): ?>
          <?php foreach ($errors['coarse_pin_id'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group<?php if (array_key_exists('fine_pin_id', $errors)): ?> has-error<?php endif; ?>">
        <label for="finepinid" class="control-label col-md-4">Fine Pin</label>
        <div class="col-md-8">
          <select id="finepinid" name="finepinid">
            <?php foreach ($driverPins as $driverPin): ?>
            <option value="<?=$this->e($driverPin->getId())?>"><?=$this->e($driverPin->getName())?></option>
            <?php endforeach; ?>
          </select>
          <?php if (array_key_exists('fine_pin_id', $errors)): ?>
          <?php foreach ($errors['fine_pin_id'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group<?php if (array_key_exists('mapping', $errors)): ?> has-error<?php endif; ?>">
        <label for="mapping" class="control-label col-md-4">Pin Mapping</label>
        <div class="col-md-3">
          <input class="form-control" type="number" name="mapping" id="mapping" placeholder="64" maxlength="3"/>
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
          <input class="form-control" type="number" name="overlapvalue" id="overlapvalue" placeholder="127" maxlength="3"/>
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
            <input class="form-control" type="number" name="defaultdelay" id="defaultdelay" placeholder="5" maxlength="5"/>
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