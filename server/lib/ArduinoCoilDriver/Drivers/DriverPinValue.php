<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use ArduinoCoilDriver\Drivers\Base\DriverPinValue as BaseDriverPinValue;
use ArduinoCoilDriver\Drivers\Map\DriverPinValueTableMap;

/**
 * Skeleton subclass for representing a row from the 'driver_pin_values' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class DriverPinValue extends BaseDriverPinValue
{
    public static function createFromValue($driverPinId, $stateId, $value) {
        // create driver pin value
        $driverPinValue = new self();
    
        // get a write connection
        $connection = Propel::getWriteConnection(DriverPinValueTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // set parameters
        $driverPinValue->setDriverPinId($driverPinId);
        $driverPinValue->setStateId($stateId);
        $driverPinValue->setValue($value);
        
        // save
        $driverPinValue->save();
        
        // commit transaction
        $connection->commit();
        
        return $driverPinValue;
    }
}
