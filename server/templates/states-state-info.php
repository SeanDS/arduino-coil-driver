<?php $this->layout('states-template') ?>
<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <h2>State <?=$this->e($state->getId());?> Values</h2>
    <table class="table table-bordered table-hover table-striped">
      <thead>
        <th class="col-md-4 text-center">Driver</th>
        <th class="col-md-4 text-center">Pin</th>
        <th class="col-md-4 text-center">Value</th>
      </thead>
      <tbody>
        <?php if (count($state->getAllDriverPinValues())): ?>
        <?php foreach ($state->getAllDriverPinValues() as $driver => $pinValues): ?>
        <?php foreach ($pinValues as $pinValue): ?>
        <tr>
          <td class="text-center"><?=$this->e($pinValue->getDriverPin()->getDriver()->getName());?></td>
          <td class="text-center"><?=$this->e($pinValue->getDriverPin()->getPin())?></td>
          <td><?=$this->e($pinValue->getValue())?></td>
        </tr>
        <?php endforeach; ?>
        <?php endforeach; ?>
        <?php else: ?>
        <tr>
            <td class="text-center" colspan="3">There are no outputs defined. <a href="drivers.php">View drivers</a>.</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>