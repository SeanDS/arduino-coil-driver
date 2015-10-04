<?php $this->layout('template', ['page' => 'dashboard', 'title' => 'Dashboard' . (!is_null($title) ? " - $title" : "")]) ?>
        <div class="container">
            <h1>Dashboard</h1>
            <p class="lead">Welcome, <?=$this->e($user->getName())?></p>
            <?=$this->section('content')?>
        </div>