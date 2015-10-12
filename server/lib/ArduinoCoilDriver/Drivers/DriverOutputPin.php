<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\Base\DriverOutputPin as BaseDriverOutputPin;
use ArduinoCoilDriver\Drivers\Map\DriverOutputPinTableMap;

/**
 * Skeleton subclass for representing a row from the 'driver_output_pins' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class DriverOutputPin extends BaseDriverOutputPin
{
    public function preDelete(ConnectionInterface $connection = null) {
        if (is_null($connection)) {
            // get a write connection
            $connection = Propel::getWriteConnection(DriverOutputPinTableMap::DATABASE_NAME);
        }
        
        // start transaction
        $connection->beginTransaction();
        
        // delete driver output pin values
        foreach ($this->getDriverOutputPinValues() as $driverOutputPinValue) {
            $driverOutputPinValue->delete();
        }
        
        // commit transaction
        $connection->commit();
        
        return true;
    }
    
    public function postInsert(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver output pin with id %d', $this->getId()));
    }
    
    public function postUpdate(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver output pin id %d updated', $this->getId()));
    }
    
    public function postDelete(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver output pin id %d deleted', $this->getId()));
    }
}
