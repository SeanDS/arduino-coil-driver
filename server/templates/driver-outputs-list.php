<h2><?=$this->e($driver->getName())?> Driver Outputs</h2>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <?php if ($messageId === 1): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        Output added.
    </div>
    <?php elseif ($messageId === 2): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        Output saved.
    </div>
    <?php elseif ($messageId === 3): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        Output deleted.
    </div>
    <?php endif; ?>
    <table class="table table-bordered table-hover table-striped">
        <thead>
            <th class="col-md-3">Output</th>
            <th class="col-md-6">Pins</th>
            <th class="col-md-3">Actions</th>
        </thead>
        <tbody>
        <?php if ($driver->getDriverOutputCount()): ?>
            <?php foreach($driver->getDriverOutputs() as $driverOutput): ?>
            <tr>
                <td><?=$this->e($driverOutput->getName())?></td>
                <td><?php $this->insert('driver-output-list-pins', ['driverOutput' => $driverOutput]) ?></td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="drivers.php?do=editoutput&amp;oid=<?=$this->e($driverOutput->getId())?>" class="btn btn-xs btn-default">Edit</a>
                        <a href="drivers.php?do=deleteoutput&amp;oid=<?=$this->e($driverOutput->getId())?>" class="btn btn-xs btn-danger">Delete</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No driver outputs defined.</td>
            </tr>
        <?php endif; ?>
        </tbody>    
    </table>
    <a class="btn btn-default" href="drivers.php?do=newoutput&amp;id=<?=$this->e($driver->getId())?>" role="button">New Output</a>
  </div>
</div>