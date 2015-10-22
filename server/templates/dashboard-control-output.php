<?php $this->layout('dashboard-template') ?>
<h2>Control <?=$this->e($driverOutput->getName())?></h2>
<div class="row">
    <div class="col-xs-12 col-md-6 col-md-offset-3">
        <?php $this->insert('dashboard-control-output-panel', ['driverOutput' => $driverOutput]) ?>
    </div>
</div>
<?php $this->insert('dashboard-control-output-script') ?>