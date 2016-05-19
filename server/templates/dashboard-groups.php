<div class="row">
    <div class="col-md-12">
        <div class="text-center">
            <?php if ($groups->count()): ?>
            <div class="btn-group-vertical" role="group">
            <?php foreach ($groups as $group): ?>
                <a href="index.php?do=controlgroup&amp;oid=<?=$this->e($group->getId())?>" class="btn btn-lg btn-primary" style="width: 100%; margin: 1px 0 1px 0;"><?=$this->e($group->getName())?></a>
            <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-info">No groups.</p>
            <?php endif; ?>
        </div>
    </div>
</div>