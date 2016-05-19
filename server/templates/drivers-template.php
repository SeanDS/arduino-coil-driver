<?php $this->layout('template', ['page' => 'drivers', 'title' => 'Drivers' . (!is_null($title) ? " - $title" : "")]) ?>
        <div class="container">
            <h1>Drivers</h1>
            <?=$this->section('content')?>
        </div>