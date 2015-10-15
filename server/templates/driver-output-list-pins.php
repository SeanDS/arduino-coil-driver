<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-3">Pin</th>
        <th class="col-md-6">Type</th>
        <th class="col-md-3">Value</th>
    </thead>
    <tbody>
    <?php if (count($driverOutput->getDriverOutputPins())): ?>
        <?php foreach($driverOutput->getDriverOutputPins() as $outputPin): ?>
        <tr>
            <td><?=$this->e($outputPin->getDriverPin()->getPin())?></td>
            <td><?=$this->e($outputPin->getType())?></td>
            <td><?=$this->e($outputPin->getDriverPin()->getLatestDriverPinValue()->getValue())?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">No driver output pins defined.</td>
        </tr>
    <?php endif; ?>
    </tbody>    
</table>