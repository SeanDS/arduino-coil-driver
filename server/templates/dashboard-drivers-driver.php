<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title text-center"><?=$this->e($driver->getName())?></h1>
    </div>
    <div class="panel-body">
        <?php if ($driver->getDriverOutputs()->count()): ?>
        <?php foreach ($driver->getDriverOutputs() as $output): ?>
            <a href="index.php?do=controloutput&amp;oid=<?=$this->e($output->getId())?>" class="btn btn-sm btn-default" style="width: 100%; margin: 1px 0 1px 0;"><?=$this->e($output->getName())?></a>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="text-muted">There are no outputs associated with this driver.</p>
        <?php endif; ?>
    </div>
    <div class="panel-footer text-center"><a href="drivers.php?do=edit&amp;id=<?=$this->e($driver->getId())?>">Edit</a></div>
</div>