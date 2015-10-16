<?php

require('require.php');

use ArduinoCoilDriver\Drivers\DriverQuery;
use ArduinoCoilDriver\Drivers\DriverOutputQuery;

function getDriverOutputFromGet($returnUrl = 'index.php') {
    global $logger;
    global $templates;

    // load driver by HTTP_GET id
    $id = filter_input(INPUT_GET, 'oid', FILTER_VALIDATE_INT);
    
    // get driver output
    $driverOutput = DriverOutputQuery::create()->findPK($id);
    
    if ($driverOutput === null) {
        $logger->addWarning(sprintf('Specified driver output id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified driver output not found.', 'returnUrl' => $returnUrl]);
        
        exit();
    }
    
    return $driverOutput;
}

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {   
    // list drivers and output groups

    // get drivers
    $drivers = DriverQuery::create()->orderByName()->find();
    
    echo $templates->render('dashboard-list', ['drivers' => $drivers]);
} elseif ($do === 'controloutput') {
    // control driver output
    
    // get driver output
    $driverOutput = getDriverOutputFromGet();
    
    echo $templates->render('dashboard-control-output', ['driverOutput' => $driverOutput]);
}

?>