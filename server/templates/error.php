<?php $this->layout('template', ['title' => 'Error']) ?>
        <div class="container">
            <h1>Error</h1>
            <p class="text-danger"><?=$this->e($message)?></p>
            <a class="btn btn-primary" href="<?=$returnUrl?>">Back</a>
        </div>