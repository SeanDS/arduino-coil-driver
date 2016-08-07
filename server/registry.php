<?php

require('require.php');

use ArduinoCoilDriver\Drivers\DriverQuery;
use ArduinoCoilDriver\Drivers\UnregisteredDriver;
use ArduinoCoilDriver\Drivers\UnregisteredDriverQuery;
use ArduinoCoilDriver\Payload\StatusReceivePayload;

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {
    
} elseif ($do === 'report') {    
    // get driver's IP address
    $driverIp = $_SERVER['REMOTE_ADDR'];
    
    $infoLogger->addInfo(sprintf('New checkin from driver %s', $driverIp));
    
    $payload = StatusReceivePayload::createFromReceivedPost($HTTP_RAW_POST_DATA);
    
    if (! ($payload instanceof StatusReceivePayload)) {
        // we only handle status payloads here, and this isn't one
        $infoLogger->addInfo('Payload received was not a status. Ignoring.');
        
        exit();
    }
    
    // check if this driver is already known
    $driver = DriverQuery::create()->filterByMac($payload->getMac())->filterByIp($driverIp)->findOne();
    
    if (is_null($driver)) {
        // driver isn't known
        $infoLogger->addInfo('Driver isn\'t registered');
        
        // is it in the list of unregistered drivers?
        $unregisteredDriver = UnregisteredDriverQuery::create()->filterByMac($payload->getMac())->filterByIp($_SERVER['REMOTE_ADDR'])->findOne();
        
        if (is_null($unregisteredDriver)) {
            // driver is new
            $infoLogger->addInfo('Driver isn\'t in unregistered list');
            
            // add it to list of unregistered
            $newUnregisteredDriver = new UnregisteredDriver();
            $newUnregisteredDriver->setMac($payload->getMac());
            $newUnregisteredDriver->setIp($driverIp);
            $newUnregisteredDriver->setLastCheckIn('now');
            $newUnregisteredDriver->save();
            
            $infoLogger->addInfo(sprintf('Driver added to unregistered list with ID %d', $newUnregisteredDriver->getId()));
        } else {
            // driver is known
            $infoLogger->addInfo(sprintf('Driver is known by unregistered driver ID %d - checking in', $unregisteredDriver->getId()));
            
            // update last checkin
            $unregisteredDriver->setLastCheckIn('now');
            $unregisteredDriver->save();
        }
    } else {
        // driver is known
        $infoLogger->addInfo(sprintf('Driver is known by driver ID %d - checking in', $driver->getId()));
        
        // update
        $driver->setLastCheckIn('now');
        $driver->setCoilContact($payload->getCoilContact());
        $driver->save();
    }
}

?>