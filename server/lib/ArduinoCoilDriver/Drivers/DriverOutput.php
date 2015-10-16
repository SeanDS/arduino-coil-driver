<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\Base\DriverOutput as BaseDriverOutput;
use ArduinoCoilDriver\Drivers\Map\DriverOutputTableMap;
use ArduinoCoilDriver\Drivers\Map\DriverOutputPinTableMap;
use ArduinoCoilDriver\Exceptions\IdenticalOutputPinsException;
use ArduinoCoilDriver\Exceptions\ValidationException;

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
    public static function create($driver, $name, $coarsePin, $finePin, $mapping, $overlapValue, $defaultDelay) {
        if ($coarsePin == $finePin) {
            throw new IdenticalOutputPinsException();
        }
        
        $driverOutput = new self();
        
        // get a write connection
        $connection = Propel::getWriteConnection(DriverOutputTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // set parameters
        $driverOutput->setDriverId($driver->getId());
        $driverOutput->setName($name);
        $driverOutput->setMapping($mapping);
        $driverOutput->setOverlapValue($overlapValue);
        $driverOutput->setDefaultDelay($defaultDelay);
        
        // validate
        if (! $driverOutput->validate()) {
            $connection->rollback();
            
            throw new ValidationException($driverOutput);
        }
        
        // save
        $driverOutput->save();
        
        // create driver output pins
        DriverOutputPin::create($driverOutput, $coarsePin, DriverOutputPinTableMap::COL_TYPE_COARSE);
        DriverOutputPin::create($driverOutput, $finePin, DriverOutputPinTableMap::COL_TYPE_FINE);
        
        // commit transaction
        $connection->commit();
        
        return $driverPin;
    }

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
        
        $logger->addInfo(sprintf('Driver output inserted with id %d', $this->getId()));
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
