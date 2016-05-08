<div class="row">
  <?php if ($groups->count()): ?>
  <?php foreach ($groups as $group): ?>
  <div class="col-md-6">
    <?php $this->insert('dashboard-groups-group', ['group' => $group]) ?>
  </div>
  <?php endforeach; ?>
  <?php else: ?>
  <div class="col-md-6">
    <p class="text-info">No groups.</p>
  </div>
  <?php endif; ?>
</div>