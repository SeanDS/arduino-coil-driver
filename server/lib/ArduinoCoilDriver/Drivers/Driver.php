<?php

namespace ArduinoCoilDriver\Drivers;

use Exception;
use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\Base\Driver as BaseDriver;
use ArduinoCoilDriver\Drivers\Map\DriverTableMap;
use ArduinoCoilDriver\Payload\SendPayload;
use ArduinoCoilDriver\Payload\ReceivePayload;
use ArduinoCoilDriver\Payload\StatusReceivePayload;
use ArduinoCoilDriver\Payload\OutputReceivePayload;
use ArduinoCoilDriver\States\State;
use ArduinoCoilDriver\Exceptions\NoContactException;
use ArduinoCoilDriver\Exceptions\InvalidJsonException;
use ArduinoCoilDriver\Exceptions\ConflictingStatusException;

/**
 * Skeleton subclass for representing a row from the 'drivers' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Driver extends BaseDriver
{
    public static function createFromUnregistered(UnregisteredDriver $unregisteredDriver) {
        global $logger;
    
        // get the unregistered driver's status and output payloads
        try {
            $statusPayload = $unregisteredDriver->getStatus();
            $outputPayload = $unregisteredDriver->getOutputs();
        } catch (NoContactException $e) {
            $logger->addWarning(sprintf('Unregistered driver id %d cannot be contacted', $unregisteredDriver->getId()));
            
            throw $e;
        } catch (InvalidJsonException $e) {
            $logger->addWarning(sprintf('Unregistered driver id %d returned invalid JSON message', $unregisteredDriver->getId()));
            
            throw $e;
        }
        
        echo $statusPayload->getIp() . "," . $statusPayload->getMac();
        
        // do some validation
        if ($statusPayload->getMac() !== $unregisteredDriver->getMac() || $statusPayload->getIp() !== $unregisteredDriver->getIp()) {
            $logger->addWarning(sprintf('Status reported by unregistered driver id %d differs from the record', $unregisteredDriver->getId()));
            
            throw new ConflictingStatusException($unregisteredDriver);
        }
        
        // create driver
        $driver = new self();
        
        // get a write connection
        $connection = Propel::getWriteConnection(DriverTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // set parameters
        $driver->setName($unregisteredDriver->getMac());
        $driver->setMac($unregisteredDriver->getMac());
        $driver->setIp($unregisteredDriver->getIp());
        $driver->setAdded('now');
        $driver->setLastCheckIn('1970-01-01');
        $driver->setCoilContact($statusPayload->getCoilContact());
        
        // save
        $driver->save();
        
        // create a state
        $state = State::init();
        
        // save state
        $state->save();
        
        // add driver pins
        foreach ($outputPayload->getPinValues() as $pin => $value) {
            DriverPin::createFromPin($driver->getId(), $state, $pin, $value);
        }
        
        // commit transaction
        $connection->commit();
        
        return $driver;
    }
    
    public function snapToState(State $state) {
        // snaps the outputs in this driver to the specified state, or nearest before
        
        // get this driver's outputs
        $outputs = $this->getDriverOutputs();
        
        // set each output to the required value
        foreach ($outputs as $output) {
            $output->snapToState($state);
        }
    }
    
    public function postInsert(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver inserted with id %d', $this->getId()));
    }
    
    public function postUpdate(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver id %d updated', $this->getId()));
    }
    
    public function preDelete(ConnectionInterface $connection = null) {
        if (is_null($connection)) {
            // get a write connection
            $connection = Propel::getWriteConnection(DriverTableMap::DATABASE_NAME);
        }
        
        // start transaction
        $connection->beginTransaction();
        
        // delete driver pins
        foreach ($this->getDriverPins() as $driverPin) {
            $driverPin->delete();
        }
        
        // delete driver outputs
        foreach ($this->getDriverOutputs() as $driverOutput) {
            $driverOutput->delete();
        }
        
        // commit transaction
        $connection->commit();
        
        return true;
    }
    
    public function postDelete(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Driver id %d deleted', $this->getId()));
    }
    
    private function contact($get) {
        global $logger;
        
        $logger->addInfo(sprintf('Contacting driver id %d: %s', $this->getId(), $get));
        
        // return payload
        return ReceivePayload::payloadFromGet($this->getIp(), 80, $get);
    }
    
    public function dispatch(SendPayload $payload) {
        global $logger;
        
        $logger->addInfo(sprintf('Dispatching payload to driver id %d', $this->getId()));
        
        // send payload to driver and get message
        return $this->contact($payload->getRequest());
    }
    
    public function updatePinsFromOutputReceivePayload(OutputReceivePayload $payload, State $state = null) {
        $pinValues = $payload->getPinValues();
        
        // get a write connection
        $connection = Propel::getWriteConnection(DriverTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // create new state if needed
        if ($state == null) {
            $state = State::init();
        }
        
        // update pins
        foreach ($this->getDriverPins() as $driverPin) {
            if (in_array($driverPin->getPin(), array_keys($pinValues))) {
                $driverPin->updateValue($pinValues[$driverPin->getPin()], $state);
            }
        }
        
        // commit transaction
        $connection->commit();
    }
    
    public function getStatus() {
        global $logger;
        
        $logger->addInfo(sprintf('Getting status of driver id %d', $this->getId()));
        
        $payload = $this->contact("/status");
        
        if (! $payload instanceof StatusReceivePayload) {
            throw new Exception("Received payload is not a StatusReceivePayload");
        }
        
        return $payload;
    }
    
    public function getOutputs() {
        global $logger;
        
        $logger->addInfo(sprintf('Getting outputs of driver id %d', $this->getId()));
        
        $payload = $this->contact("/outputs");
        
        if (! $payload instanceof OutputReceivePayload) {
            throw new Exception("Received payload is not a OutputReceivePayload");
        }
        
        return $payload;
    }
}
