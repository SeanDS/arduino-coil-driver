<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/arduinocoildriver.css">
        
        <title><?=$this->e($mainTitle . " - " . $title)?></title>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><?=SERVER_NAME?></a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-left">
                      <li<?php if ($page == 'dashboard'): ?> class="active"<?php endif; ?>><a href="index.php">Dashboard</a></li>
                      <li<?php if ($page == 'drivers'): ?> class="active"<?php endif; ?>><a href="drivers.php">Drivers</a></li>
                      <li<?php if ($page == 'states'): ?> class="active"<?php endif; ?>><a href="states.php">States</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                      <li<?php if ($page == 'user'): ?> class="active"<?php endif; ?>><a href="user.php">User</a></li>
                    </ul>
                </div>
            </div>
        </nav>
<?=$this->section('content')?>
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
