<?php $this->layout('drivers-template') ?>
<h2><?=$driver->getName()?> Status</h2>
<div class="row">
  <div class="col-md-4">
    <table class="table table-bordered table-hover table-striped">
      <thead>
        <th class="col-md-6">Attribute</th>
        <th class="col-md-6">Value</th>
      </thead>
      <tbody>
        <tr>
          <td>MAC address</td>
          <td><?=$status->getMac()?></td>
        </tr>
        <tr>
          <td>IP address</td>
          <td><?=$status->getIp()?></td>
        </tr>
        <tr>
          <td>Software version</td>
          <td><?=$status->getVersion()?></td>
        </tr>
        <tr>
          <td>SD card present</td>
          <td><?=($status->getSdCard()) ? "Yes" : "No"?></td>
        </tr>
        <tr>
          <td>Coil contact</td>
          <td><?=($status->getCoilContact()) ? "<span class=\"text-danger\">Yes</span> (first: " . $this->e($status->getFirstCoilContactString()) . ", second: " . $this->e($status->getSecondCoilContactString()) . ")" : "<span class=\"text-success\">No</span>"?></td>
        </tr>
      </tbody>
    </table>
    <p>Status message received in <?=$this->e(sprintf("%.2fs", $status->getTimeTaken()))?>.</p>
    <a class="btn btn-default" href="drivers.php" role="button">Back</a>
  </div>
</div>