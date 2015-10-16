<?php $this->layout('template', ['page' => 'dashboard', 'title' => 'Dashboard' . (!is_null($title) ? " - $title" : "")]) ?>
        <div class="container">
            <h1>Dashboard</h1>
            <?=$this->section('content')?>
        </div>