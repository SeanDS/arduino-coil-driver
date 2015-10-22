<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title text-center"><?=$this->e($driverOutput->getName())?></h1>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block driver-output-adjust-button" data-offset="-1" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">-1</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block driver-output-adjust-button" data-offset="-5" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">-5</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block driver-output-adjust-button" data-offset="-20" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">-20</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block driver-output-adjust-button" data-offset="-100" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">-100</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block driver-output-adjust-button" data-offset="-500" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">-500</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <p class="pin-value text-center driver-output-adjust-current-value" data-driver-output-id="<?=$this->e($driverOutput->getId())?>"><?=$this->e($driverOutput->getValue())?></p>
                <form>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">Set to</div>
                            <input class="form-control driver-output-adjust-set-text" type="text" name="custom" id="custom" placeholder="0" maxlength="5" value="0" data-driver-output-id="<?=$this->e($driverOutput->getId())?>"/>
                            <span class="input-group-btn">
                                <button class="btn btn-primary driver-output-adjust-set-button" type="button" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">Go</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <p>
                            <input class="switchramp" type="checkbox" data-on-text="Ramp" data-off-text="Snap" data-handle-width="100" data-label-text="Mode" data-driver-output-id="<?=$this->e($driverOutput->getId())?>" checked>
                        </p>
                    </div>
                </form>
                <?php if ($driverOutput->getDriver()->getCoilContact()): ?>
                <?php $this->insert('driver-coil-contact', ['driver' => $driverOutput->getDriver()]) ?>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block driver-output-adjust-button" data-offset="1" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">+1</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block driver-output-adjust-button" data-offset="5" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">+5</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block driver-output-adjust-button" data-offset="20" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">+20</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block driver-output-adjust-button" data-offset="100" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">+100</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block driver-output-adjust-button" data-offset="500" data-driver-output-id="<?=$this->e($driverOutput->getId())?>">+500</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>