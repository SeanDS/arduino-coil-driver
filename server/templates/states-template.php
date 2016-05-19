<?php $this->layout('template', ['page' => 'states', 'title' => 'States' . (!is_null($title) ? " - $title" : "")]) ?>
        <div class="container">
            <h1>States</h1>
            <?=$this->section('content')?>
        </div>