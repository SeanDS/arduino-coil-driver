<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\Base\DriverOutputPinValue as BaseDriverOutputPinValue;

/**
 * Skeleton subclass for representing a row from the 'driver_output_pin_values' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class DriverOutputPinValue extends BaseDriverOutputPinValue
{
    public function postInsert(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver output pin value with id %d', $this->getId()));
    }
    
    public function postUpdate(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver output pin value id %d updated', $this->getId()));
    }
    
    public function postDelete(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver output pin value id %d deleted', $this->getId()));
    }
}
