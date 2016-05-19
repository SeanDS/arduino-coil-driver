<?php $this->layout('dashboard-template') ?>
<h2>Control <?=$this->e($group->getName())?></h2>
<div class="row">
    <?php if (count($group->getOutputViewOutputs())): ?>
    <?php foreach ($group->getOutputViewOutputs() as $outputViewOutput): ?>
    <div class="col-xs-12 col-md-6">
        <?php $this->insert('dashboard-control-output-panel', ['driverOutput' => $outputViewOutput->getDriverOutput()]) ?>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="col-xs-12 col-md-6">
        <p class="text-warning">There are no outputs associated with this group. <a href="groups.php?do=edit&amp;id=<?=$this->e($group->getId())?>">Edit group</a>.</p>
    </div>
    <?php endif; ?>
</div>
<?php $this->insert('dashboard-control-output-script') ?>