<?php $this->layout('states-template') ?>
<h2>New State Bookmark</h2>
<div class="row">
    <div class="col-md-4">
        <form action="states.php?do=newbookmark&amp;id=<?=$this->e($state->getId())?>" method="post" class="form-horizontal">
            <div class="form-group<?php if (array_key_exists('description', $errors)): ?> has-error<?php endif; ?>">
                <label for="description" class="control-label col-md-4">Description</label>
                <div class="col-md-8">
                    <textarea class="form-control" name="description" id="description" placeholder="Details of new state bookmark" maxlength="255"></textarea>
                    <?php if (array_key_exists('description', $errors)): ?>
                    <?php foreach ($errors['description'] as $error): ?>
                    <span class="help-block"><?=$this->e($error)?></span>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-offset-4 col-md-8">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>