<?php

require('require.php');

use ArduinoCoilDriver\Drivers\DriverQuery;
use ArduinoCoilDriver\Drivers\DriverOutputQuery;
use ArduinoCoilDriver\Exceptions\InvalidJsonException;

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

// perform check for user credentials (require.php doesn't redirect to login.php to avoid issue with AJAX)
if (! array_key_exists('userId', $_SESSION)) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if ($do === 'ramp') {
    // ramp driver output
    
    // get driver output
    $driverOutput = getDriverOutputFromGet();
    
    // get inputs
    $get = filter_input_array(
        INPUT_GET,
        array(
            'value'  =>  FILTER_VALIDATE_INT
        )
    );
    
    if (is_null($get['value'])) {
        exit();
    }
    
    // set value
    try {
        $message = $driverOutput->setValue($get['value']);
    } catch (InvalidJsonException $e) {
        throw $e;
    } catch (NoContactException $e) {
        throw $e;
    }
    
    // output JSON array with new value
    print_r($message);
}

?>