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
    public static function create($driverOutput, $driverPin, $type) {
        $driverOutputPin = new self();
        
        // get a write connection
        $connection = Propel::getWriteConnection(DriverOutputPinTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // set parameters
        $driverOutputPin->setDriverOutputId($driverOutput->getId());
        $driverOutputPin->setDriverPinId($driverPin->getId());
        $driverOutputPin->setType($type);
        
        // save
        $driverOutputPin->save();
        
        // commit transaction
        $connection->commit();
        
        return $driverOutputPin;
    }
    
    public function postInsert(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver output pin inserted with id %d', $this->getId()));
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
