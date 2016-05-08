<?php $this->layout('groups-template') ?>
<h2>Edit Group</h2>
<div class="row">
  <div class="col-md-4">
    <form action="groups.php?do=edit&amp;id=<?=$this->e($group->getId())?>" method="post" class="form-horizontal">
      <div class="form-group<?php if (array_key_exists('name', $errors)): ?> has-error<?php endif; ?>">
        <label for="name" class="control-label col-md-4">Name</label>
        <div class="col-md-8">
          <input class="form-control" type="text" name="name" id="name" placeholder="Name" maxlength="32" value="<?=$this->e($group->getName())?>"/>
          <?php if (array_key_exists('name', $errors)): ?>
          <?php foreach ($errors['name'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group<?php if (array_key_exists('display_order', $errors)): ?> has-error<?php endif; ?>">
        <label for="display_order" class="control-label col-md-4">Display Order</label>
        <div class="col-md-8">
          <input class="form-control" type="number" name="display_order" id="display_order" placeholder="e.g. 5" maxlength="3" value="<?=$this->e($group->getDisplayOrder())?>"/>
          <?php if (array_key_exists('display_order', $errors)): ?>
          <?php foreach ($errors['display_order'] as $error): ?>
          <span class="help-block"><?=$this->e($error)?></span>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group<?php if (array_key_exists('driver_outputs', $errors)): ?> has-error<?php endif; ?>">
        <label class="control-label col-md-4">Driver Outputs</label>
        <?php if (array_key_exists('driver_outputs', $errors)): ?>
        <?php foreach ($errors['driver_outputs'] as $error): ?>
        <span class="help-block"><?=$this->e($error)?></span>
        <?php endforeach; ?>
        <?php endif; ?>
        <div class="col-md-8">
        <?php foreach ($driverOutputs as $driverOutput): ?>
          <div class="form-group">
            <label for="driver_outputs_<?=$driverOutput->getId()?>"><?=$driverOutput->getFullName()?></label>
            <div class="input-group col-md-8">
              <span class="input-group-addon">
                <input type="checkbox" name="driver_outputs[]" id="driver_outputs_<?=$driverOutput->getId()?>" value="<?=$driverOutput->getId()?>"<?php if (in_array($driverOutput->getId(), $checkedOutputIds)): ?> checked<?php endif; ?>>
              </span>
              <input class="form-control" type="number" name="driver_outputs_display_order[]" id="driver_outputs_display_order_<?=$driverOutput->getId()?>" placeholder="Display order" maxlength="3"<?php if (in_array($driverOutput->getId(), $checkedOutputIds)): ?> value="<?=$checkedOutputDisplayOrders[$driverOutput->getId()]?>"<?php endif; ?>/>
            </div>
          </div>
        <?php endforeach; ?>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="reset" class="btn btn-danger">Reset</button>
        </div>
      </div>
    </form>
  </div>
</div>