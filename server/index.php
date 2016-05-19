<?php

require('require.php');

use ArduinoCoilDriver\Drivers\DriverQuery;
use ArduinoCoilDriver\Drivers\DriverOutputQuery;
use ArduinoCoilDriver\Outputs\OutputViewQuery;

function getDriverOutputFromGet($returnUrl = 'index.php') {
    global $errorLogger;
    global $templates;

    // load driver by HTTP_GET id
    $id = filter_input(INPUT_GET, 'oid', FILTER_VALIDATE_INT);
    
    // get driver output
    $driverOutput = DriverOutputQuery::create()->findPK($id);
    
    if ($driverOutput === null) {
        $errorLogger->addError(sprintf('Specified driver output id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified driver output not found.', 'returnUrl' => $returnUrl]);
        
        exit();
    }
    
    return $driverOutput;
}

function getOutputGroupFromGet($returnUrl = 'index.php') {
    global $errorLogger;
    global $templates;

    // get group id from HTTP_GET
    $id = filter_input(INPUT_GET, 'oid', FILTER_VALIDATE_INT);
    
    // get output group
    $group = OutputViewQuery::create()->findPK($id);
    
    if ($group === null) {
        $errorLogger->addError(sprintf('Specified output group id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified output group not found.', 'returnUrl' => $returnUrl]);
        
        exit();
    }
    
    return $group;
}

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {   
    // list drivers and output groups

    // get drivers
    $drivers = DriverQuery::create()->orderByName()->find();
    
    // get groups
    $groups = OutputViewQuery::create()->orderByDisplayOrder()->find();
    
    echo $templates->render('dashboard', ['drivers' => $drivers, 'groups' => $groups, 'tankUrls' => $tankUrls]);
} elseif ($do === 'controloutput') {
    // control driver output
    
    // get driver output
    $driverOutput = getDriverOutputFromGet();
    
    echo $templates->render('dashboard-control-output', ['driverOutput' => $driverOutput]);
} elseif ($do == 'controlgroup') {
    // control output group
    
    // get group
    $group = getOutputGroupFromGet();
    
    echo $templates->render('dashboard-control-group', ['group' => $group]);
}

?>