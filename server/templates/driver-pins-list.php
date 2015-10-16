<h2><?=$this->e($driver->getName())?> Driver Pins</h2>
<div class="row">
  <div class="col-md-4 col-md-offset-4">
    <table class="table table-bordered table-hover table-striped">
        <thead>
            <th class="col-md-6 text-center">Pin</th>
            <th class="col-md-6 text-center">Value</th>
        </thead>
        <tbody>
        <?php if ($driver->getDriverPins()->count()): ?>
            <?php foreach($driver->getDriverPins() as $driverPin): ?>
            <tr>
                <td class="text-center"><?=$this->e($driverPin->getPin())?></td>
                <td class="text-center"><?=$this->e($driverPin->getLatestDriverPinValue()->getValue())?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">No driver pins.</td>
            </tr>
        <?php endif; ?>
        </tbody>    
    </table>
  </div>
</div>