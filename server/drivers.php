<?php

require('require.php');

use ArduinoCoilDriver\Drivers\Driver;
use ArduinoCoilDriver\Drivers\DriverQuery;
use ArduinoCoilDriver\Drivers\UnregisteredDriver;
use ArduinoCoilDriver\Drivers\UnregisteredDriverQuery;

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'message'    =>  FILTER_SANITIZE_STRING
        )
    );
    
    $drivers = DriverQuery::create()->orderByName()->find();
    
    echo $templates->render('drivers', ['drivers' => $drivers]);
} elseif ($do === 'unregistered') {
    // list unregistered drivers
    
    $drivers = UnregisteredDriverQuery::create()->orderByLastCheckin()->find();
    
    echo $templates->render('drivers-unregistered', ['drivers' => $drivers]);
}

?>