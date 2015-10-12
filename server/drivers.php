<?php

require('require.php');

use Propel\Runtime\Propel;
use ArduinoCoilDriver\Drivers\Driver;
use ArduinoCoilDriver\Drivers\DriverQuery;
use ArduinoCoilDriver\Drivers\UnregisteredDriver;
use ArduinoCoilDriver\Drivers\UnregisteredDriverQuery;
use ArduinoCoilDriver\Drivers\Map\DriverTableMap;
use ArduinoCoilDriver\Exceptions\NoContactException;
use ArduinoCoilDriver\Exceptions\InvalidJsonException;

function getDriverFromGet($returnUrl = 'drivers.php') {
    // load driver by HTTP_GET id
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    // get driver
    $driver = DriverQuery::create()->findPK($id);
    
    if ($driver === null) {
        $logger->addWarning(sprintf('Specified driver id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified driver not found.', 'returnUrl' => $returnUrl]);
        
        exit();
    }
    
    return $driver;
}

function getUnregisteredDriverFromGet($returnUrl = 'drivers.php') {
    // load driver by HTTP_GET id
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    // get unregistered driver
    $unregisteredDriver = UnregisteredDriverQuery::create()->findPK($id);
    
    if (is_null($unregisteredDriver)) {
        $logger->addWarning(sprintf('Specified unregistered driver id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified unregistered driver not found.', 'returnUrl' => 'drivers.php?do=unregistered']);
        
        exit();
    }
    
    return $unregisteredDriver;
}

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'mid'    =>  FILTER_VALIDATE_INT
        )
    );
    
    $drivers = DriverQuery::create()->orderByName()->find();
    
    echo $templates->render('drivers', ['drivers' => $drivers, 'messageId' => $get['mid']]);
} elseif ($do === 'unregistered') {
    // list unregistered drivers
    
    $drivers = UnregisteredDriverQuery::create()->orderByLastCheckIn()->find();
    
    echo $templates->render('drivers-unregistered', ['drivers' => $drivers]);
} elseif ($do === 'register') {
    // register a driver
    
    // get specified unregistered driver
    $unregisteredDriver = getUnregisteredDriverFromGet();
    
    $logger->addInfo(sprintf('User wants to register unregistered driver id %d', $unregisteredDriver->getId()));
    
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
    
        echo $templates->render('error', ['message' => $e->getMessage(), 'returnUrl' => 'drivers.php?do=unregistered']);
        
        exit();
    } catch (InvalidJsonException $e) {
        $connection->rollback();
    
        echo $templates->render('error', ['message' => $e->getMessage(), 'returnUrl' => 'drivers.php?do=unregistered']);
            
        exit();
    } catch (ConflictingStatusException $e) {
        $connection->rollback();
        
        echo $templates->render('error', ['message' => $e->getMessage(), 'returnUrl' => 'drivers.php?do=unregistered']);
            
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
    if ($post['name']) {
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
        $logger->addWarning(sprintf('Sriver id %d cannot be contacted', $driver->getId()));
        
        echo $templates->render('error', ['message' => 'Specified driver cannot be contacted.', 'returnUrl' => 'drivers.php']);
            
        exit();
    } catch (InvalidJsonException $e) {
        $logger->addWarning(sprintf('Unregistered driver id %d returned invalid JSON message', $unregisteredDriver->getId()));
        
        echo $templates->render('error', ['message' => 'Specified driver returned an invalid message.', 'returnUrl' => 'drivers.php']);
            
        exit();
    }
    
    // print status
    echo $templates->render('drivers-status', ['driver' => $driver, 'status' => $statusPayload]);
}

?>