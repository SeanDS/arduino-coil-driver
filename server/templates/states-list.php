<?php if ($messageId === 5): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    State loaded.
</div>
<?php elseif ($messageId === 6): ?>
<div class="alert alert-warning alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    State is already loaded.
</div>
<?php elseif ($messageId === 7): ?>
<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    One or more drivers could not be contacted. <a href="drivers.php">Check network connectivity</a>.
</div>
<?php endif; ?>
<h2>Bookmarked States</h2>
<?php if ($messageId === 1): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Bookmark added.
</div>
<?php elseif ($messageId === 2): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Bookmark saved.
</div>
<?php elseif ($messageId === 3): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Bookmark deleted.
</div>
<?php endif; ?>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-2 text-center">Last Loaded</th>
        <th class="col-md-2 text-center">User</th>
        <th class="col-md-5 text-center">Description</th>
        <th class="col-md-3 text-center">Actions</th>
    </thead>
    <tbody>
    <?php if ($bookmarksPager->getNbResults()): ?>
        <?php foreach($bookmarksPager as $state): ?>
        <tr<?php if ($state == $currentState): ?> class="success"<?php endif; ?>>
            <td class="text-center"><?php $this->insert('date-time-span', ['time' => $state->getTime(), 'raw' => true]) ?></td>
            <td class="text-center"><?=$this->e($state->getUser()->getName())?></td>
            <td><?=$this->e($state->getStateBookmark()->getDescription())?></td>
            <td class="text-center">
                <div class="btn-group">
                    <?php if ($state != $currentState): ?>
                    <a href="states.php?do=load&amp;id=<?=$this->e($state->getId())?>" class="btn btn-xs btn-default">Load</a>
                    <?php endif; ?>
                    <a href="states.php?do=info&amp;id=<?=$this->e($state->getId())?>" class="btn btn-xs btn-default">Info</a>
                    <a href="states.php?do=editbookmark&amp;id=<?=$this->e($state->getId())?>" class="btn btn-xs btn-default">Edit</a>
                    <a href="states.php?do=deletebookmark&amp;id=<?=$this->e($state->getId())?>" class="btn btn-xs btn-danger">Delete</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td class="text-center" colspan="6">No states.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<nav>
    <ul class="pager">
        <li><a href="states.php?page=<?=$this->e($bookmarksPager->getFirstPage())?>">First</a></li>
        <?php foreach ($bookmarksPager->getLinks(5) as $link): ?>
        <li><a href="states.php?page=<?=$this->e($link)?>"><?=$this->e($link)?></a></li>
        <?php endforeach; ?>
        <li><a href="states.php?page=<?=$this->e($bookmarksPager->getLastPage())?>">Last</a></li>
    </ul>
</nav>

<h2>All States</h2>
<?php if ($messageId === 4): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    State deleted.
</div>
<?php endif; ?>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-4 text-center">Last Loaded</th>
        <th class="col-md-4 text-center">User</th>
        <th class="col-md-4 text-center">Actions</th>
    </thead>
    <tbody>
    <?php if ($statesPager->getNbResults()): ?>
        <?php foreach($statesPager as $state): ?>
        <tr<?php if ($state == $currentState): ?> class="success"<?php endif; ?>>
            <td class="text-center"><?php $this->insert('date-time-span', ['time' => $state->getTime()]) ?></td>
            <td class="text-center"><?=$this->e($state->getUser()->getName())?></td>
            <td class="text-center">
                <div class="btn-group">
                    <?php if ($state != $currentState): ?>
                    <a href="states.php?do=load&amp;id=<?=$this->e($state->getId())?>" class="btn btn-xs btn-default">Load</a>
                    <?php endif; ?>
                    <a href="states.php?do=info&amp;id=<?=$this->e($state->getId())?>" class="btn btn-xs btn-default">Info</a>
                    <?php if ($state->getStateBookmark() === null): ?>
                    <a href="states.php?do=newbookmark&amp;id=<?=$this->e($state->getId())?>" class="btn btn-xs btn-default">Bookmark</a>
                    <?php endif; ?>
                    <?php if ($state->isDeletable()): ?>
                    <a href="states.php?do=delete&amp;id=<?=$this->e($state->getId())?>" class="btn btn-xs btn-danger">Delete</a>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td class="text-center" colspan="6">No states.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<p class="text-info">Some states are unable to be deleted as they are the most up-to-date states for one or more pins.</p>
<nav>
    <ul class="pager">
        <li><a href="states.php?spage=<?=$this->e($statesPager->getFirstPage())?>">First</a></li>
        <?php foreach ($statesPager->getLinks(5) as $link): ?>
        <li><a href="states.php?spage=<?=$this->e($link)?>"><?=$this->e($link)?></a></li>
        <?php endforeach; ?>
        <li><a href="states.php?spage=<?=$this->e($statesPager->getLastPage())?>">Last</a></li>
    </ul>
</nav>