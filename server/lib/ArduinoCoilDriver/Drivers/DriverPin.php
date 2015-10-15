<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\Base\DriverPin as BaseDriverPin;
use ArduinoCoilDriver\Drivers\Map\DriverPinTableMap;
use ArduinoCoilDriver\Drivers\Map\DriverPinValueTableMap;
use ArduinoCoilDriver\Drivers\Map\DriverOutputPinValueTableMap;
use ArduinoCoilDriver\States\Map\StateTableMap;

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
    public function getName() {
        return sprintf("Pin %d", $this->getPin());
    }

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
    
    public function getLatestDriverPinValue() {
        return DriverPinValueQuery::create()->addJoin(DriverPinValueTableMap::COL_STATE_ID, StateTableMap::COL_ID, Criteria::INNER_JOIN)->add(DriverPinValueTableMap::COL_ID, $this->getId(), Criteria::EQUAL)->addDescendingOrderByColumn(StateTableMap::COL_TIME)->findOne();
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
