<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\Base\DriverOutput as BaseDriverOutput;
use ArduinoCoilDriver\Drivers\Map\DriverOutputTableMap;

/**
 * Skeleton subclass for representing a row from the 'driver_outputs' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class DriverOutput extends BaseDriverOutput
{
    public function preDelete(ConnectionInterface $connection = null) {
        if (is_null($connection)) {
            // get a write connection
            $connection = Propel::getWriteConnection(DriverOutputTableMap::DATABASE_NAME);
        }
        
        // start transaction
        $connection->beginTransaction();
        
        // delete driver output pins
        foreach ($this->getDriverOutputPins() as $driverOutputPin) {
            $driverOutputPin->delete();
        }
        
        // commit transaction
        $connection->commit();
        
        return true;
    }
    
    public function postInsert(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver output with id %d', $this->getId()));
    }
    
    public function postUpdate(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver output id %d updated', $this->getId()));
    }
    
    public function postDelete(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver output id %d deleted', $this->getId()));
    }
}
