<?php $this->layout('template', ['page' => 'groups', 'title' => 'Groups' . (!is_null($title) ? " - $title" : "")]) ?>
        <div class="container">
            <h1>Groups</h1>
            <?=$this->section('content')?>
        </div>