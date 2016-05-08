<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\Base\DriverOutput as BaseDriverOutput;
use ArduinoCoilDriver\Drivers\Map\DriverOutputTableMap;
use ArduinoCoilDriver\Drivers\Map\DriverOutputPinTableMap;
use ArduinoCoilDriver\Payload\SendPayload;
use ArduinoCoilDriver\Payload\OutputReceivePayload;
use ArduinoCoilDriver\Exceptions\IdenticalOutputPinsException;
use ArduinoCoilDriver\Exceptions\NoContactException;
use ArduinoCoilDriver\Exceptions\ValidationException;
use ArduinoCoilDriver\Exceptions\InvalidToggleException;

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
    
    public function setValue($value, $toggleMode) {
        // set output value
        
        if ($value > (256 * $this->getMapping())) {
            // FIXME: don't use hard-coded value here
            $value = 256 * $this->getMapping() - 1;
        } elseif ($value < 0) {
            $value = 0;
        }
    
        // get output pins
        $outputPins = $this->getOutputPins();
        
        // calculate coarse value
        $newCoarseValue = intval(floor($value / $this->getMapping()));
        
        // calculate fine value
        $newFineValue = $value % $this->getMapping();
        
        // create message
        $payload = $this->createTogglePayload($toggleMode, $outputPins['coarse'], $newCoarseValue, $outputPins['fine'], $newFineValue);
        
        $receivePayload = $this->getDriver()->dispatch($payload);
        
        if ($receivePayload instanceof OutputReceivePayload) {
            $this->getDriver()->updatePinsFromOutputReceivePayload($receivePayload);
            
            // set the updated value in the payload
            $receivePayload->setOutputValue($this->getValue());
        }
        
        return $receivePayload;
    }
    
    public function getOutputPins() {
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
    
    public function getFullName() {
        return sprintf("%s %s", $this->getDriver()->getName(), $this->getName());
    }
    
    protected function createTogglePayload($toggleMode, DriverOutputPin $pin1, $value1, DriverOutputPin $pin2, $value2) {
        $toggleMode = intval($toggleMode);
    
        if ($toggleMode !== self::TOGGLE_MODE_SNAP && $toggleMode !== self::TOGGLE_MODE_RAMP) {
            throw new InvalidToggleException('Specified toggle mode is invalid.');
        }
        
        if (! is_int($value1)) {
            throw new InvalidToggleException('Specified value1 is invalid.');
        } elseif ($value1 < 0) {
            throw new InvalidToggleException('Specified value1 cannot be negative.');
        } elseif (! is_int($value2)) {
            throw new InvalidToggleException('Specified value2 is invalid.');
        } elseif ($value2 < 0) {
            throw new InvalidToggleException('Specified value2 cannot be negative.');
        }
        
        $settings = array();
        $settings['pinmode'] = self::PIN_MODE_DUAL;
        $settings['togglemode'] = $toggleMode;   
        $settings['pin1'] = $pin1->getDriverPin()->getPin();
        $settings['value1'] = $value1;
        $settings['pin2'] = $pin2->getDriverPin()->getPin();
        $settings['value2'] = $value2;
        $settings['mapping'] = $this->getMapping();
        $settings['overlap'] = $this->getOverlapValue();
        
        if ($toggleMode === self::TOGGLE_MODE_SNAP) {
            // don't need any extra settings
        } else {
            // need delay            
            $settings['delay'] = $this->getDefaultDelay();
        }
    
        return new SendPayload("/toggle", $settings);
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
