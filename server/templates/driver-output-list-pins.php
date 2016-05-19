
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <th class="col-md-3 text-center">Pin</th>
                    <th class="col-md-6 text-center">Type</th>
                    <th class="col-md-3 text-center">Value</th>
                </thead>
                <tbody>
                <?php if ($driverOutput->getDriverOutputPins()->count()): ?>
                    <?php foreach($driverOutput->getDriverOutputPins() as $outputPin): ?>
                    <tr>
                        <td class="text-center"><?=$this->e($outputPin->getDriverPin()->getPin())?></td>
                        <td class="text-center"><?=$this->e($outputPin->getType())?></td>
                        <td class="text-center"><?=$this->e($outputPin->getDriverPin()->getLatestDriverPinValue()->getValue())?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No driver output pins defined.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <th class="col-md-3">Parameter</th>
                    <th class="col-md-3">Value</th>
                </thead>
                <tbody>
                    <tr>
                        <td>Mapping</td>
                        <td><?=$this->e($driverOutput->getMapping())?></td>
                    </tr>
                    <tr>
                        <td>Overlap value</td>
                        <td><?=$this->e($driverOutput->getOverlapValue())?></td>
                    </tr>
                    <tr>
                        <td>Default delay</td>
                        <td><?=$this->e($driverOutput->getDefaultDelay())?> ms</td>
                    </tr>
            </table>