<?php $this->layout('dashboard-template') ?>
<h2>Control <?=$this->e($group->getName())?></h2>
<div class="row">
    <?php foreach ($group->getOutputViewOutputs() as $outputViewOutput): ?>
    <div class="col-xs-12 col-md-6">
        <?php $this->insert('dashboard-control-output-panel', ['driverOutput' => $outputViewOutput->getDriverOutput()]) ?>
    </div>
    <?php endforeach; ?>
</div>
<?php $this->insert('dashboard-control-output-script') ?>