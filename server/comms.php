<?php

require('require.php');

use ArduinoCoilDriver\Drivers\DriverPinQuery;
use ArduinoCoilDriver\Drivers\DriverOutput;
use ArduinoCoilDriver\Drivers\DriverOutputQuery;
use ArduinoCoilDriver\Payload\OutputReceivePayload;
use ArduinoCoilDriver\Payload\ErrorReceivePayload;
use ArduinoCoilDriver\Exceptions\InvalidJsonException;
use ArduinoCoilDriver\Exceptions\NoContactException;
use ArduinoCoilDriver\Exceptions\InvalidToggleException;

function getDriverPinFromGet($returnUrl = 'comms.php') {
    global $errorLogger;
    global $templates;

    // load driver by HTTP_GET id
    $id = filter_input(INPUT_GET, 'pid', FILTER_VALIDATE_INT);
    
    // get driver pin
    $driverPin = DriverPinQuery::create()->findPK($id);
    
    if ($driverPin === null) {
        $errorLogger->addError(sprintf('Specified driver pin id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified driver pin not found.', 'returnUrl' => $returnUrl]);
        
        exit();
    }
    
    return $driverPin;
}

function getDriverOutputFromGet($returnUrl = 'comms.php') {
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

// start output buffering
ob_start();

// perform check for user credentials (require.php doesn't redirect to login.php to avoid issue with AJAX)
if (! array_key_exists('userId', $_SESSION)) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: application/json');
    exit(json_encode(["status" => "error", "message" => "Access forbidden. Please log in."]));
}

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if ($do == 'single') {
    // set a driver pin output
    
    // get driver pin
    $driverPin = getDriverPinFromGet();
    
    // get inputs
    $get = filter_input_array(
        INPUT_GET,
        array(
            'value'       =>  FILTER_VALIDATE_INT
        )
    );
    
    if (is_null($get['value'])) {
        exit();
    }
    
    // set value
    try {
        $message = $driverPin->setValue($get['value']);
    } catch (InvalidJsonException $e) {
        // just rethrow for now
        throw $e;
    } catch (NoContactException $e) {
        // just rethrow for now
        throw $e;
    } catch (InvalidToggleException $e) {
        // just rethrow for now
        throw $e;
    }
    
    if ($message instanceof OutputReceivePayload) {
        $status = "ok";
    
        // create JSON array with new value
        $value = json_encode([$driverPin->getId() => $message->getPinValue($driverPin->getPin())]);
    } elseif ($message instanceof ErrorReceivePayload) {
        $status = "error";
        $value = $message->getMessage();
    } else {
        $status = "error";
        $value = "Unknown receive payload";
    }
    
    header('Content-Type: application/json');
    echo json_encode(["status" => $status, "message" => $value]);
} elseif ($do === 'dual') {
    // set a driver output (a collection of two pins)
    
    // get driver output
    $driverOutput = getDriverOutputFromGet();
    
    // get inputs
    $get = filter_input_array(
        INPUT_GET,
        array(
            'value'       =>  FILTER_VALIDATE_INT,
            'togglemode'  =>  FILTER_SANITIZE_STRING
        )
    );
    
    if (is_null($get['value'])) {
        exit();
    } elseif (is_null($get['togglemode'])) {
        exit();
    }
    
    if ($get['togglemode'] === 'ramp') {
        $toggleMode = DriverOutput::TOGGLE_MODE_RAMP;
    } elseif ($get['togglemode'] === 'snap') {
        $toggleMode = DriverOutput::TOGGLE_MODE_SNAP;
    } else {
        exit();
    }
    
    // set value
    try {
        $message = $driverOutput->setValue($get['value'], $toggleMode);
    } catch (InvalidJsonException $e) {
        // just rethrow for now
        throw $e;
    } catch (NoContactException $e) {
        // just rethrow for now
        throw $e;
    } catch (InvalidToggleException $e) {
        // just rethrow for now
        throw $e;
    }
    
    if ($message instanceof OutputReceivePayload) {
        $status = "ok";
    
        // create JSON array with new value        
        $value = json_encode([$driverOutput->getId() => $message->getOutputValue()]);
    } elseif ($message instanceof ErrorReceivePayload) {
        $status = "error";
        $value = $message->getMessage();
    } else {
        $status = "error";
        $value = "Unknown receive payload";
    }
    
    header('Content-Type: application/json');
    echo json_encode(["status" => $status, "message" => $value]);
}

ob_end_flush();

?>