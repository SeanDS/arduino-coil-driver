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
use ArduinoCoilDriver\Exceptions\ConflictingStatusException;

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
    
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    // get unregistered driver
    $unregisteredDriver = UnregisteredDriverQuery::create()->findPK($get['id']);
    
    if (is_null($unregisteredDriver)) {
        $logger->addWarning(sprintf('Specified unregistered driver id %d doesn\'t exist', $get['id']));
    
        echo $templates->render('error', ['message' => 'Specified unregistered driver not found.', 'returnUrl' => 'drivers.php?do=unregistered']);
        
        exit();
    }
    
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
}

?>