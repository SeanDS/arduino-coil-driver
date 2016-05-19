<div class="row">
  <?php if ($drivers->count()): ?>
  <?php foreach ($drivers as $driver): ?>
  <div class="col-md-3">
    <?php $this->insert('dashboard-drivers-driver', ['driver' => $driver]) ?>
  </div>
  <?php endforeach; ?>
  <?php else: ?>
  <div class="col-md-3">
    <p class="text-info">No drivers.</p>
  </div>
  <?php endif; ?>
</div>