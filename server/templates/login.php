<?php $this->layout('login-template') ?>
<?php if($messageId == 1): ?>
<div class="row">
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        Logged out.
    </div>
</div>
<?php endif; ?>
<?php if($ldapConnectionIssue): ?>
<div class="row">
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        There was a problem connecting to LDAP. Please try again later.
    </div>
</div>
<?php endif; ?>
<?php $this->insert('login-form', ['badCredentials' => $badCredentials]) ?>