<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title text-center"><?=$this->e($driverOutput->getName())?></h1>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block">-1</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block">-5</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block">-20</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block">-100</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block">-500</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <p class="pin-value text-center"><?=$this->e($driverOutput->getValue())?></p>
                <form>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">Set to</div>
                            <input class="form-control" type="text" name="custom" id="custom" placeholder="0" maxlength="5" value="0"/>
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="button">Go</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <p>
                            <input class="switchramp" type="checkbox" data-on-text="Ramp" data-off-text="Snap" data-handle-width="100" data-label-text="Mode" checked>
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
                        <button class="btn btn-default btn-sm btn-block">+1</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block">+5</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block">+20</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block">+100</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn-sm btn-block">+500</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>