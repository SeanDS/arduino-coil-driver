<?php $this->layout('dashboard-template') ?>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Map</h1>
            </div>
            <div class="panel-body">
                <?php $this->insert('dashboard-svg', ['tankUrls' => $tankUrls]) ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Drivers</h1>
            </div>
            <div class="panel-body">
                <?php $this->insert('dashboard-drivers', ['drivers' => $drivers]) ?>
            </div>
            <div class="panel-footer"><a href="drivers.php">View Drivers</a></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Groups</h1>
            </div>
            <div class="panel-body">
                <?php $this->insert('dashboard-groups', ['groups' => $groups]) ?>
            </div>
            <div class="panel-footer"><a href="groups.php">View Groups</a></div>
        </div>
    </div>
</div>