<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-3">MAC</th>
        <th class="col-md-3">IP</th>
        <th class="col-md-3">Last check-in</th>
        <th class="col-md-3">Actions</th>
    </thead>
    <tbody>
    <?php if ($drivers->count()): ?>
        <?php foreach($drivers as $driver): ?>
        <tr>
            <td><?=$this->e($driver->getMac())?></td>
            <td><?=$this->e($driver->getIp())?></td>
            <td><?php $this->insert('date-time-span', ['time' => $driver->getLastCheckIn()]) ?></td>
            <td class="text-center">
                <div class="btn-group">
                    <a href="drivers.php?do=addunregistered&amp;id=<?=$this->e($driver->getId())?>" class="btn btn-xs btn-default">Add</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">No unregistered drivers.</td>
        </tr>
    <?php endif; ?>
    </tbody>    
</table>