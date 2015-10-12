<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionInterface;
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
    
    public function postInsert(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver pin inserted with id %d', $this->getId()));
    }
    
    public function postUpdate(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver pin id %d updated', $this->getId()));
    }
    
    public function preDelete(ConnectionInterface $connection = null) {
        if (is_null($connection)) {
            // get a write connection
            $connection = Propel::getWriteConnection(DriverPinTableMap::DATABASE_NAME);
        }
        
        // start transaction
        $connection->beginTransaction();
        
        // delete driver pin values
        foreach ($this->getDriverPinValues() as $driverPinValue) {
            $driverPinValue->delete();
        }
        
        // commit transaction
        $connection->commit();
        
        return true;
    }
    
    public function postDelete(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver pin id %d deleted', $this->getId()));
    }
}
