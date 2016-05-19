<?php

require('require.php');

use ArduinoCoilDriver\Drivers\DriverQuery;
use ArduinoCoilDriver\Drivers\UnregisteredDriver;
use ArduinoCoilDriver\Drivers\UnregisteredDriverQuery;

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {
    
} elseif ($do === 'report') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'message'    =>  FILTER_UNSAFE_RAW
        )
    );
    
    // get driver's IP address
    $driverIp = $_SERVER['REMOTE_ADDR'];
    
    $infoLogger->addInfo(sprintf('New checkin from driver %s', $driverIp));
    
    // check JSON string is valid
    $message = json_decode($get['message'], true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        // error
        $errorLogger->addError(sprintf('Message from %s is invalid JSON: %s', $driverIp, $get['message']));
        
        exit();
    } elseif (!is_array($message)) {
        $errorLogger->addError(sprintf('Message from %s is empty.', $driverIp));
        
        exit();
    }
    
    $requiredKeys = ['mac'];
    $missingKeys = array_diff($requiredKeys, array_flip($message));
    
    if (count($missingKeys)) {
        $errorLogger->addError(sprintf('Message from %s doesn\'t contain required key(s): %s', $driverIp, implode(',', $missingKeys)));
        
        exit();
    }
    
    $infoLogger->addInfo('Driver\'s message is valid');
    $infoLogger->addInfo('Keys: ' . implode(', ', array_keys($message)) . ', Vals: ' . implode(', ', $message));
    
    // convert MAC from base 10 to base 16
    $pieces = explode(':', $message['mac']);
    
    foreach ($pieces as $key => $piece) {
        $pieces[$key] = base_convert($piece, 10, 16);
        
        if (strlen($pieces[$key]) < 2) {
            // piece is a singular digit
            // prepend a zero
            $pieces[$key] = "0" . $pieces[$key];
        }
    }
    
    // assemble hexadecimal MAC address
    $message['mac'] = implode(':', $pieces);
    
    // check if this driver is already known
    $driver = DriverQuery::create()->filterByMac($message['mac'])->filterByIp($driverIp)->findOne();
    
    if (is_null($driver)) {
        // driver isn't known
        $infoLogger->addInfo('Driver isn\'t registered');
        
        // is it in the list of unregistered drivers?
        $unregisteredDriver = UnregisteredDriverQuery::create()->filterByMac($message['mac'])->filterByIp($_SERVER['REMOTE_ADDR'])->findOne();
        
        if (is_null($unregisteredDriver)) {
            // driver is new
            $infoLogger->addInfo('Driver isn\'t in unregistered list');
            
            // add it to list of unregistered
            $newUnregisteredDriver = new UnregisteredDriver();
            $newUnregisteredDriver->setMac($message['mac']);
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
        $driver->setCoilContact($message['coil_contact_1'] || $message['coil_contact_2']);
        $driver->save();
    }
}

?>