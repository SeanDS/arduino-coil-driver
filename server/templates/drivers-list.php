<?php if ($messageId === 1): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Driver added.
</div>
<?php elseif ($messageId === 2): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Driver saved.
</div>
<?php elseif ($messageId === 3): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Driver deleted.
</div>
<?php endif; ?>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-3 text-center">Name</th>
        <th class="col-md-2 text-center">Last check-in</th>
        <th class="col-md-3 text-center">Coil contact</th>
        <th class="col-md-1 text-center">Pins</th>
        <th class="col-md-1 text-center">Outputs</th>
        <th class="col-md-2 text-center">Actions</th>
    </thead>
    <tbody>
    <?php if ($drivers->count()): ?>
        <?php foreach($drivers as $driver): ?>
        <tr>
            <td class="text-center"><?=$this->e($driver->getName())?></td>
            <td class="text-center"><?php $this->insert('date-time-span', ['time' => $driver->getLastCheckIn()]) ?></td>
            <td class="text-center"><?php if ($driver->getCoilContact()): ?><div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Coils are touching!</div><?php else: ?><div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> OK</div><?php endif; ?></td>
            <td class="text-center"><a href="drivers.php?do=listpins&amp;id=<?=$this->e($driver->getId())?>"><?=$this->e($driver->getDriverPins()->count())?></a></td>
            <td class="text-center"><a href="drivers.php?do=listoutputs&amp;id=<?=$this->e($driver->getId())?>"><?=$this->e($driver->getDriverOutputs()->count())?></a></td>
            <td class="text-center">
                <div class="btn-group">
                    <a href="drivers.php?do=edit&amp;id=<?=$this->e($driver->getId())?>" class="btn btn-xs btn-default">Edit</a>
                    <a href="drivers.php?do=status&amp;id=<?=$this->e($driver->getId())?>" class="btn btn-xs btn-default">Status</a>
                    <a href="drivers.php?do=delete&amp;id=<?=$this->e($driver->getId())?>" class="btn btn-xs btn-danger">Delete</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No drivers.</td>
        </tr>
    <?php endif; ?>
    </tbody>    
</table>