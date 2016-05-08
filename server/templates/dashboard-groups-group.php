<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title text-center"><?=$this->e($group->getName())?></h1>
    </div>
    <div class="panel-body">
        <?php if ($group->getOutputViewOutputs()->count()): ?>
        <?php foreach ($group->getOutputViewOutputs() as $output): ?>
            <a href="index.php?do=controloutput&amp;oid=<?=$this->e($output->getDriverOutputId())?>" class="btn btn-sm btn-default" style="width: 100%; margin: 1px 0 1px 0;"><?=$this->e($output->getDriverOutput()->getFullName())?></a>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="text-muted">There are no outputs associated with this group.</p>
        <a href="groups.php?do=edit&id=<?=$group->getId()?>" class="btn btn-sm btn-default">Edit Group</a>
        <?php endif; ?>
    </div>
    <div class="panel-footer text-center"><a href="groups.php?do=edit&amp;id=<?=$group->getId()?>">Edit</a></div>
</div>