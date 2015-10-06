<?php

namespace ArduinoCoilDriver\Drivers;

use Propel\Runtime\Propel;
use ArduinoCoilDriver\Drivers\Base\Driver as BaseDriver;
use ArduinoCoilDriver\Drivers\Map\DriverTableMap;
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
        global $user;
    
        $logger->addInfo(sprintf('User wants to register unregistered driver id %d', $unregisteredDriver->getId()));
        
        // get the unregistered driver's status and output payloads
        try {
            $statusPayload = $unregisteredDriver->getStatus();
            $outputPayload = $unregisteredDriver->getOutputs();
        } catch (NoContactException $e) {
            $logger->addWarning(sprintf('Unregistered driver id %d cannot be contacted', $unregisteredDriver->getId()));
            
            $connection->rollback();
            
            throw $e;
        } catch (InvalidJsonException $e) {
            $logger->addWarning(sprintf('Unregistered driver id %d returned invalid JSON message', $unregisteredDriver->getId()));
            
            $connection->rollback();
            
            throw $e;
        }
        
        // do some validation
        if ($statusPayload->getMac() !== $unregisteredDriver->getMac() || $statusPayload->getIp() !== $unregisteredDriver->getIp()) {
            $logger->addWarning(sprintf('Status reported by unregistered driver id %d differs from the record', $unregisteredDriver->getId()));
            
            $connection->rollback();
            
            throw new ConflictingStatusException($unregisteredDriver);
        }
        
        // create driver
        $driver = new self();
        
        // get a write connection
        $connection = Propel::getWriteConnection(DriverTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // set parameters
        $driver->setName("Driver " . $unregisteredDriver->getMac());
        $driver->setMac($unregisteredDriver->getMac());
        $driver->setIp($unregisteredDriver->getIp());
        $driver->setAdded('now');
        $driver->setLastCheckIn('1970-01-01');
        $driver->setCoilContact($statusPayload->getCoilContact());
        
        // save
        $driver->save();
        
        // create a state
        $state = new State();
        $state->setUserId($user->getId());
        $state->setTime('now');
        
        // save state
        $state->save();
        
        // add driver pins
        foreach ($outputPayload->getPinValues() as $pin => $value) {
            DriverPin::createFromPin($driver->getId(), $state->getId(), $pin, $value);
        }
        
        // commit transaction
        $connection->commit();
        
        return $driver;
    }
    
    public function getDriverPinCount() {
        return DriverPinQuery::create()->filterByDriverId($this->getId())->count();
    }
    
    public function getDriverOutputCount() {
        return DriverOutputQuery::create()->filterByDriverId($this->getId())->count();
    }
}
