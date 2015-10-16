<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\Base\DriverOutput as BaseDriverOutput;
use ArduinoCoilDriver\Drivers\Map\DriverOutputTableMap;
use ArduinoCoilDriver\Drivers\Map\DriverOutputPinTableMap;
use ArduinoCoilDriver\Payload\SendPayload;
use ArduinoCoilDriver\Exceptions\IdenticalOutputPinsException;
use ArduinoCoilDriver\Exceptions\NoContactException;
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
    const PIN_MODE_SINGLE   = 0;
    const PIN_MODE_DUAL     = 1;
    const TOGGLE_MODE_SNAP  = 0;
    const TOGGLE_MODE_RAMP  = 1;

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
    
    public function getValue() {
        // get output value
    
        // get output pins
        $outputPins = $this->getOutputPins();
        
        return $this->getMapping() * $outputPins['coarse']->getDriverPin()->getLatestDriverPinValue()->getValue() + $outputPins['fine']->getDriverPin()->getLatestDriverPinValue()->getValue();
    }
    
    public function setValue($value) {
        // set output value
    
        // get output pins
        $outputPins = $this->getOutputPins();
        
        // calculate coarse value
        $newCoarseValue = floor($value / $this->getMapping());
        
        // calculate fine value
        $newFineValue = $value % $this->getMapping();
        
        // create message
        $payload = $this->createRampPayload($outputPins['coarse'], $outputPins['fine'], $newCoarseValue, $newFineValue, $this->getMapping(), $this->getOverlapValue(), $this->getDefaultDelay());
        
        return $this->getDriver()->dispatch($payload);
    }
    
    protected function getOutputPins() {
        // get output pins
        $outputPins = DriverOutputPinQuery::create()->filterByDriverOutputId($this->getId())->find();
        
        if ($outputPins->count() != 2) {
            // FIXME: only one coarse and one fine pin are supported currently
            throw new Exception('There can currently only be two output pins associated with each driver output.');
        }
        
        $pins = array('coarse' => null, 'fine' => null);
        
        foreach ($outputPins as $outputPin) {
            if ($outputPin->getType() === DriverOutputPinTableMap::COL_TYPE_COARSE) {
                $pins['coarse'] = $outputPin;
            } elseif ($outputPin->getType() === DriverOutputPinTableMap::COL_TYPE_FINE) {
                $pins['fine'] = $outputPin;
            }
        }
        
        if (in_array(null, $pins, true)) {
            throw new Exception('One or both of the two pins associated with this driver output is/are not of type \'coarse\' or \'fine\'.');
        }
        
        return $pins;
    }
    
    protected function createRampPayload($pin1, $pin2, $value1, $value2, $mapping, $overlap, $delay) {
        return new SendPayload("/toggle",
            array(
                'pinmode'     =>  self::PIN_MODE_DUAL,
                'togglemode'  =>  self::TOGGLE_MODE_RAMP,
                'pin1'        =>  $pin1->getDriverPin()->getPin(),
                'pin2'        =>  $pin2->getDriverPin()->getPin(),
                'value1'      =>  $value1,
                'value2'      =>  $value2,
                'map'         =>  $mapping,
                'overlap'     =>  $overlap,
                'delay'       =>  $delay
            )
        );
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
