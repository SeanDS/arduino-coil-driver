<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use ArduinoCoilDriver\Drivers\Base\DriverPin as BaseDriverPin;
use ArduinoCoilDriver\Drivers\Map\DriverPinTableMap;

/**
 * Skeleton subclass for representing a row from the 'driver_pins' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class DriverPin extends BaseDriverPin
{
    public static function createFromPin($driverId, $stateId, $pin, $value) {
        // create driver pin
        $driverPin = new self();
    
        // get a write connection
        $connection = Propel::getWriteConnection(DriverPinTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // set parameters
        $driverPin->setDriverId($driverId);
        $driverPin->setPin($pin);
        
        // save
        $driverPin->save();
        
        // create and set driver pin value
        DriverPinValue::createFromValue($driverPin->getId(), $stateId, $value);
        
        // commit transaction
        $connection->commit();
        
        return $driverPin;
    }
}
