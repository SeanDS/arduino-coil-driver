<?php if ($message == 'editsuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Expense saved.
</div>
<?php endif; ?>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-4">Name</th>
        <th class="col-md-4">Last check-in</th>
        <th class="col-md-2">Coil contact</th>
        <th class="col-md-2">Actions</th>
    </thead>
    <tbody>
    <?php if ($drivers->count()): ?>
        <?php foreach($drivers as $driver): ?>
        <tr>
            <td><?=$this->e($driver->getName())?></td>
            <td><?=$this->e($driver->getLastCheckIn())?></td>
            <td><?php if ($driver->getCoilContact()): ?><div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Coils are touching!</div><?php else: ?><div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> OK</div><?php endif; ?></td>
            <td class="text-center">
                <div class="btn-group">
                    <a href="drivers.php?do=edit&amp;id=<?=$this->e($driver->getId())?>" class="btn btn-xs btn-default">Edit</a>
                    <a href="drivers.php?do=delete&amp;id=<?=$this->e($driver->getId())?>" class="btn btn-xs btn-danger">Delete</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">No drivers.</td>
        </tr>
    <?php endif; ?>
    </tbody>    
</table>