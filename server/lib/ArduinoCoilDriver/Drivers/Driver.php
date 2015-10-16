<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\Base\Driver as BaseDriver;
use ArduinoCoilDriver\Drivers\Map\DriverTableMap;
use ArduinoCoilDriver\Payload\SendPayload;
use ArduinoCoilDriver\States\State;
use ArduinoCoilDriver\Exceptions\NoContactException;
use ArduinoCoilDriver\Exceptions\InvalidJsonException;
use ArduinoCoilDriver\Exceptions\ConflictingStatusException;
use ArduinoCoilDriver\Payload\StatusPayload;
use ArduinoCoilDriver\Payload\OutputPayload;

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
    
        // start clock
        $startTime = microtime(true);
        
        $logger->addInfo(sprintf('Contacting driver id %d', $this->getId()));
        
        // open socket
        $socket = @fsockopen($this->getIp(), 80, $errorCode, $errorString, DEFAULT_SOCKET_TIMEOUT);
        
        // if socket isn't open
        if (! $socket) {            
            throw new NoContactException($errorCode, $errorString);
        }
        
        // ask for status
        fwrite($socket, "GET " . $get . " HTTP/1.1\r\nHOST: " . $this->getIp() . "\r\n\r\n");

        $message = "";
        
        $contentFlag = false;
        
        // compile returned message
        while (! feof($socket)) {
            $line = fgets($socket, MAXIMUM_SOCKET_LINE_LENGTH);
            
            if ($contentFlag) {
                $message .= $line;
            } else {
                if ($line == "\r\n" && !$contentFlag) {
                    // this is the last line
                    $contentFlag = true;
                }
            }
        }
        
        // close socket
        fclose($socket);
        
        // stop clock
        $endTime = microtime(true);
        
        return $message;
    }
    
    public function dispatch(SendPayload $payload) {
        global $logger;
        
        $logger->addInfo(sprintf('Dispatching payload to driver id %d', $this->getId()));
        
        // send payload to driver and get message
        $startTime = microtime(true);
        $message = $this->contact($payload->getRequest());
        $endTime = microtime(true);
        
        $outputPayload = new OutputPayload($message, $endTime - $startTime);
        
        $this->updatePinsFromPayload($outputPayload);
    }
    
    protected function updatePinsFromPayload(OutputPayload $payload) {
        $pinValues = $payload->getPinValues();
        
        // get a write connection
        $connection = Propel::getWriteConnection(DriverTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // create new state
        $state = State::init();
        
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
    
        $startTime = microtime(true);
        
        $logger->addInfo(sprintf('Getting status of driver id %d', $this->getId()));
        
        $message = $this->contact("/status");
        
        $endTime = microtime(true);
        
        return new StatusPayload($message, $endTime - $startTime);
    }
    
    public function getOutputs() {
        global $logger;
    
        $startTime = microtime(true);
        
        $logger->addInfo(sprintf('Getting outputs of driver id %d', $this->getId()));
        
        $message = $this->contact("/outputs");
        
        $endTime = microtime(true);
        
        return new OutputPayload($message, $endTime - $startTime);
    }
}
