<div class="row">
    <?php foreach ($drivers as $driver): ?>
    <div class="col-md-6">
        <?php $this->insert('dashboard-drivers-driver', ['driver' => $driver]) ?>
    </div>
    <?php endforeach; ?>
</div>