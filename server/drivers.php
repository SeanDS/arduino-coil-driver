<?php

require('require.php');

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use ArduinoCoilDriver\Drivers\Driver;
use ArduinoCoilDriver\Drivers\DriverOutput;
use ArduinoCoilDriver\Drivers\DriverQuery;
use ArduinoCoilDriver\Drivers\DriverOutputQuery;
use ArduinoCoilDriver\Drivers\DriverPinQuery;
use ArduinoCoilDriver\Drivers\UnregisteredDriverQuery;
use ArduinoCoilDriver\Drivers\Map\DriverTableMap;
use ArduinoCoilDriver\Drivers\Map\DriverPinTableMap;
use ArduinoCoilDriver\Drivers\Map\DriverOutputTableMap;
use ArduinoCoilDriver\Drivers\Map\DriverOutputPinTableMap;
use ArduinoCoilDriver\States\State;
use ArduinoCoilDriver\Exceptions\NoContactException;
use ArduinoCoilDriver\Exceptions\InvalidJsonException;
use ArduinoCoilDriver\Exceptions\IdenticalOutputPinsException;
use ArduinoCoilDriver\Exceptions\ValidationException;
use ArduinoCoilDriver\Exceptions\LatestStateAlreadyLoadedException;

function getDriverFromGet($returnUrl = 'drivers.php') {
    global $errorLogger;
    global $templates;

    // load driver by HTTP_GET id
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    // get driver
    $driver = DriverQuery::create()->findPK($id);
    
    if ($driver === null) {
        $errorLogger->addError(sprintf('Specified driver id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified driver not found.', 'returnUrl' => $returnUrl]);
        
        exit();
    }
    
    return $driver;
}

function getDriverOutputFromGet($returnUrl = 'drivers.php?do=listoutputs') {
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

function getUnregisteredDriverFromGet($returnUrl = 'drivers.php?do=unregistered') {
    global $errorLogger;
    global $templates;

    // load driver by HTTP_GET id
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    // get unregistered driver
    $unregisteredDriver = UnregisteredDriverQuery::create()->findPK($id);
    
    if (is_null($unregisteredDriver)) {
        $errorLogger->addError(sprintf('Specified unregistered driver id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified unregistered driver not found.', 'returnUrl' => $returnUrl]);
        
        exit();
    }
    
    return $unregisteredDriver;
}

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {
    // list drivers

    $get = filter_input_array(
        INPUT_GET,
        array(
            'mid'    =>  FILTER_VALIDATE_INT
        )
    );
    
    $drivers = DriverQuery::create()->orderByName()->find();
    
    echo $templates->render('drivers', ['drivers' => $drivers, 'messageId' => $get['mid']]);
} elseif ($do === 'listunregistered') {
    // list unregistered drivers
    
    $drivers = UnregisteredDriverQuery::create()->orderByLastCheckIn()->find();
    
    echo $templates->render('drivers-unregistered', ['drivers' => $drivers]);
} elseif ($do === 'register') {
    // register a driver
    
    // get specified unregistered driver
    $unregisteredDriver = getUnregisteredDriverFromGet();
    
    $infoLogger->addInfo(sprintf('User wants to register unregistered driver id %d', $unregisteredDriver->getId()));
    
    /*
     * ok, we're ready to add a new driver
     */
     
    // get a write connection
    $connection = Propel::getWriteConnection(DriverTableMap::DATABASE_NAME);
    
    // start a transaction
    $connection->beginTransaction();
    
    // create a new driver
    try {
        $driver = Driver::createFromUnregistered($unregisteredDriver);
    } catch (NoContactException $e) {
        $connection->rollback();
    
        echo $templates->render('error', ['message' => $e->getMessage(), 'returnUrl' => 'drivers.php?do=listunregistered']);
        
        exit();
    } catch (InvalidJsonException $e) {
        $connection->rollback();
    
        echo $templates->render('error', ['message' => $e->getMessage(), 'returnUrl' => 'drivers.php?do=listunregistered']);
            
        exit();
    } catch (ConflictingStatusException $e) {
        $connection->rollback();
        
        echo $templates->render('error', ['message' => $e->getMessage(), 'returnUrl' => 'drivers.php?do=listunregistered']);
            
        exit();
    }
    
    $driver->save();
    
    // delete unregistered entry
    $unregisteredDriver->delete();
    
    // commit
    $connection->commit();
    
    // redirect user
    header('Location: drivers.php?mid=1');
} elseif ($do === 'edit') {
    // edit a driver
    
    // get driver
    $driver = getDriverFromGet();
    
    // check for POST data
    $post = filter_input_array(
        INPUT_POST,
        array(
            'name'  =>  FILTER_SANITIZE_STRING
        )
    );
    
    // process HTTP_POST data if submitted
    if (! empty($post)) {
        $driver->setName($post['name']);
        
        if ($driver->validate()) {
            $driver->save();
        
            header('Location: drivers.php?mid=2');
        } else {
            // compile list of errors
            $errors = sortValidationErrorsByProperty($driver);
        }
    }
    
    echo $templates->render('drivers-edit', ['driver' => $driver, 'errors' => $errors]);
} elseif ($do === 'delete') {
    // delete a driver
    
    // get driver
    $driver = getDriverFromGet();
    
    // check for POST data
    $confirm = filter_input(INPUT_POST, 'confirm', FILTER_VALIDATE_BOOLEAN);
    
    // process HTTP_POST data if submitted
    if ($confirm) {
        // get a write connection
        $connection = Propel::getWriteConnection(DriverTableMap::DATABASE_NAME);
        
        // start a transaction
        $connection->beginTransaction();
        
        // delete
        $driver->delete();
        
        // commit
        $connection->commit();
        
        header('Location: drivers.php?mid=3');
    }
    
    echo $templates->render('drivers-delete', ['driver' => $driver]);
} elseif ($do === 'status') {
    // get driver status
    
    // get driver
    $driver = getDriverFromGet();
    
    // get status
    try {
        $statusPayload = $driver->getStatus();
    } catch (NoContactException $e) {
        $errorLogger->addError(sprintf('Driver id %d cannot be contacted', $driver->getId()));
        
        echo $templates->render('error', ['message' => 'Specified driver cannot be contacted.', 'returnUrl' => 'drivers.php']);
            
        exit();
    } catch (InvalidJsonException $e) {
        $errorLogger->addError(sprintf('Unregistered driver id %d returned invalid JSON message', $unregisteredDriver->getId()));
        
        echo $templates->render('error', ['message' => 'Specified driver returned an invalid message.', 'returnUrl' => 'drivers.php']);
            
        exit();
    }
    
    // print status
    echo $templates->render('drivers-status', ['driver' => $driver, 'status' => $statusPayload]);
} elseif ($do === 'sync') {
    // synchronise the server with this driver's pin settings
    
    // get driver
    $driver = getDriverFromGet();
    
    try {
        // synchronise
        $driver->synchronise();
    } catch (NoContactException $e) {
        $errorLogger->addError(sprintf('Driver %s could not be contacted', $driver->getName()));
        
        echo $templates->render('error', ['message' => 'Specified driver could not be contacted.', 'returnUrl' => 'drivers.php']);
            
        exit();
    }
    
    header('Location: drivers.php?mid=4');
} elseif ($do === 'syncall') {
    // synchronise the server with all drivers' pin settings
    
    // get drivers
    $drivers = DriverQuery::create()->find();
    
    // get the current state, for reference
    $currentState = State::getCurrentState();
    
    // create a new state for the updated values
    $newState = State::init();
    
    try {
        // synchronise each driver
        
        foreach ($drivers as $driver) {
            $driver->synchronise($newState);
        }
    } catch (Exception $e) {
        $errorLogger->addError(sprintf('Error during full sync: %s', $e->getMessage()));
        
        // roll back to last state
        try {
            $currentState->load();
        } catch (LatestStateAlreadyLoadedException $e) {
            // Well, that's good. No damage done.
        }
        
        // delete the new state
        $newState->delete();
        
        // display error        
        echo $templates->render('error', ['message' => 'There was an error during synchronisation. The current state has been restored.', 'returnUrl' => 'drivers.php']);
            
        exit();
    }
    
    header('Location: drivers.php?mid=5');
} elseif ($do === 'listpins') {
    // list driver pins
    
    // get driver
    $driver = getDriverFromGet();
    
    echo $templates->render('driver-pins', ['driver' => $driver]);
} elseif ($do === 'listoutputs') {
    // list driver outputs
    
    $get = filter_input_array(
        INPUT_GET,
        array(
            'mid'    =>  FILTER_VALIDATE_INT
        )
    );
    
    // get driver
    $driver = getDriverFromGet();
    
    echo $templates->render('driver-outputs', ['driver' => $driver, 'messageId' => $get['mid']]);
} elseif ($do === 'newoutput') {
    // add driver output
    
    // get driver
    $driver = getDriverFromGet();
    
    // get driver pins that aren't already a driver output pin
    $driverPins = DriverPinQuery::create()->addJoin(DriverPinTableMap::COL_ID, DriverOutputPinTableMap::COL_DRIVER_PIN_ID, Criteria::LEFT_JOIN)->add(DriverOutputPinTableMap::COL_DRIVER_PIN_ID, null, Criteria::ISNULL);
    
    // check for POST data
    $post = filter_input_array(
        INPUT_POST,
        array(
            'name'          =>  FILTER_SANITIZE_STRING,
            'coarsepinid'   =>  FILTER_VALIDATE_INT,
            'finepinid'     =>  FILTER_VALIDATE_INT,
            'mapping'       =>  FILTER_VALIDATE_INT,
            'overlapvalue'  =>  FILTER_VALIDATE_INT,
            'defaultdelay'  =>  FILTER_VALIDATE_INT
        )
    );
    
    if (! empty($post)) {
        // new output submitted
        
        // get pins
        $coarsePin = DriverPinQuery::create()->findPK($post['coarsepinid']);
        $finePin = DriverPinQuery::create()->findPK($post['finepinid']);
        
        if (is_null($coarsePin) || is_null($finePin)) {
            $errors['coarse_pin_id'][] = "A coarse pin must be specified";
            $errors['fine_pin_id'][] = "A fine pin must be specified";
        } else {
            // create output
            try {
                DriverOutput::create($driver, $post['name'], $coarsePin, $finePin, $post['mapping'], $post['overlapvalue'], $post['defaultdelay']);
                
                header('Location: drivers.php?do=listoutputs&id=' . $driver->getId() . '&mid=1');
            } catch (IdenticalOutputPinsException $e) {
                $errors['coarse_pin_id'][] = "The coarse pin cannot be the same as the fine pin";
                $errors['fine_pin_id'][] = "The fine pin cannot be the same as the coarse pin";
            } catch (ValidationException $e) {
                $errors = $e->getErrors();
            }
        }
    }
    
    echo $templates->render('driver-output-add', ['driver' => $driver, 'driverPins' => $driverPins, 'errors' => $errors]);
} elseif ($do === 'editoutput') {
    // edit driver output
    
    // get driver output
    $driverOutput = getDriverOutputFromGet();
    
    // check for POST data
    $post = filter_input_array(
        INPUT_POST,
        array(
            'name'          =>  FILTER_SANITIZE_STRING,
            'mapping'       =>  FILTER_VALIDATE_INT,
            'overlapvalue'  =>  FILTER_VALIDATE_INT,
            'defaultdelay'  =>  FILTER_VALIDATE_INT
        )
    );
    
    // process HTTP_POST data if submitted
    if (! empty($post)) {
        $driverOutput->setName($post['name']);
        $driverOutput->setMapping($post['mapping']);
        $driverOutput->setOverlapValue($post['overlapvalue']);
        $driverOutput->setDefaultDelay($post['defaultdelay']);
        
        if ($driverOutput->validate()) {
            $driverOutput->save();
        
            header('Location: drivers.php?do=listoutputs&id=' . $driverOutput->getDriver()->getId() . '&mid=2');
        } else {
            // compile list of errors
            $errors = sortValidationErrorsByProperty($driverOutput);
        }
    }
    
    echo $templates->render('driver-output-edit', ['driverOutput' => $driverOutput, 'errors' => $errors]);
} elseif ($do === 'deleteoutput') {
    // delete driver output
    
    // get driver output
    $driverOutput = getDriverOutputFromGet();
    
    // check for POST data
    $confirm = filter_input(INPUT_POST, 'confirm', FILTER_VALIDATE_BOOLEAN);
    
    // process HTTP_POST data if submitted
    if ($confirm) {
        // get a write connection
        $connection = Propel::getWriteConnection(DriverOutputTableMap::DATABASE_NAME);
        
        // start a transaction
        $connection->beginTransaction();
        
        // delete
        $driverOutput->delete();
        
        // commit
        $connection->commit();
        
        header('Location: drivers.php?do=listoutputs&id=' . $driverOutput->getDriver()->getId() . '&mid=3');
    }
    
    echo $templates->render('driver-output-delete', ['driverOutput' => $driverOutput]);
}

?>