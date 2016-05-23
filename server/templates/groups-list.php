<?php if ($messageId === 1): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Group added.
</div>
<?php elseif ($messageId === 2): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Group saved.
</div>
<?php elseif ($messageId === 3): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Group deleted.
</div>
<?php endif; ?>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-2 text-center">Display Order</th>
        <th class="col-md-3 text-center">Name</th>
        <th class="col-md-4 text-center">Drivers</th>
        <th class="col-md-3 text-center">Actions</th>
    </thead>
    <tbody>
    <?php if ($groups->count()): ?>
        <?php foreach($groups as $group): ?>
        <tr>
            <td class="text-center"><?=$this->e($group->getDisplayOrder())?></td>
            <td class="text-center"><?=$this->e($group->getName())?></td>
            <td class="text-center">
              <ul class="list-unstyled">
                <?php foreach ($group->getOutputViewOutputs() as $outputDriverOutput): ?>
                <li><?=$outputDriverOutput->getDriverOutput()->getFullName()?></li>
                <?php endforeach; ?>
              </ul>
            </td>
            <td class="text-center">
                <div class="btn-group">
                    <a href="index.php?do=controlgroup&amp;oid=<?=$this->e($group->getId())?>" class="btn btn-xs btn-default">View</a>
                    <a href="groups.php?do=edit&amp;id=<?=$this->e($group->getId())?>" class="btn btn-xs btn-default">Edit</a>
                    <a href="groups.php?do=delete&amp;id=<?=$this->e($group->getId())?>" class="btn btn-xs btn-danger">Delete</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td class="text-center" colspan="6">No groups.</td>
        </tr>
    <?php endif; ?>
    </tbody>    
</table>