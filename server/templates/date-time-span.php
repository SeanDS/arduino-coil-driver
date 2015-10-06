<?php

$seconds = time() - $time->format('U');
$minutes = floor($seconds / 60);

?>

<?php if ($seconds < 5): ?>
<span class="text-success">just now</span>
<?php elseif ($seconds < 60): ?>
<span class="text-success"><?=$this->e($seconds)?> seconds ago</span>
<?php elseif ($minutes == 1): ?>
<span class="text-warning"><?=$this->e($minutes)?> minute ago</span>
<?php elseif ($minutes < 60): ?>
<span class="text-warning"><?=$this->e($minutes)?> minutes ago</span>
<?php elseif ($seconds >= time()): ?>
<span class="text-danger">never</span>
<?php else: ?>
<span class="text-danger"><?=$this->e($time->format(DATETIME_FORMAT))?></span>
<?php endif; ?>