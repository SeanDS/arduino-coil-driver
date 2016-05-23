<?php

namespace ArduinoCoilDriver\States;

use DateTime;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\DriverQuery;
use ArduinoCoilDriver\Drivers\DriverPin;
use ArduinoCoilDriver\Drivers\DriverOutputPin;
use ArduinoCoilDriver\Drivers\DriverPinValueQuery;
use ArduinoCoilDriver\Drivers\DriverOutputPinQuery;
use ArduinoCoilDriver\States\StateBookmark;
use ArduinoCoilDriver\States\Base\State as BaseState;
use ArduinoCoilDriver\Exceptions\CurrentStateUndeletableException;
use ArduinoCoilDriver\Exceptions\LatestStateAlreadyLoadedException;

/**
 * Skeleton subclass for representing a row from the 'states' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class State extends BaseState
{
    public static function init() {
        global $user;
    
        return self::newState();
    }
    
    public static function newState($save = true) {
        global $user;
        
        $newState = new self();
        $newState->setUser($user);
        $newState->setTime('now');
        
        if ($save) {
            $newState->save();
        }
        
        return $newState;
    }
    
    public function load() {
        // loads the values from this state
        
        // check if this state is the latest (we don't need to load)
        if ($this == self::getCurrentState()) {
            throw new LatestStateAlreadyLoadedException($this);
        }
        
        // create a new state for the loaded values
        $newState = self::newState();
        
        // get drivers
        $drivers = DriverQuery::create()->find();
        
        // snap each driver to this state, or nearest before
        foreach ($drivers as $driver) {
            $driver->snapToValuesFromState($this, $newState);
        }
        
        // move bookmark, if any, to new state
        $bookmark = $this->getStateBookmark();
        
        if ($bookmark != null) {
            $newBookmark = new StateBookmark();
            $newBookmark->setState($newState);
            $newBookmark->setDescription($bookmark->getDescription());
            $newBookmark->save();
            
            // delete old bookmark
            $bookmark->delete();
        }
    }
    
    public function getAllDriverPinValues() {
        // gets the driver pin values not only associated with this state but those of the
        // next most up to date states for other drivers (i.e. this is a list of values
        // associated with this state)
        
        // driver pins
        $pins = DriverOutputPinQuery::create()->find();
        
        // values
        $values = array();
        
        // get values for this state
        foreach ($pins as $pin) {
            $values[$pin->getDriverPin()->getDriverId()][$pin->getId()] = $this->getDriverPinValueForDriverOutputPin($pin);
        }
        
        return $values;
    }
    
    public function getValuesForDriverOutputPins(Array $outputs) {
        // gets the values associated with the specified output pins and this state
        
        // array to hold values
        $values = array();
        
        foreach ($outputs as $key => $output) {
            $values[$key] = $this->getDriverPinValueForDriverOutputPin($output)->getValue();
        }
        
        return $values;
    }
    
    public function getDriverPinValueForDriverOutputPin(DriverOutputPin $outputPin) {
        // gets the DriverPinValue associated with the specified driver output pin and this state
        
        return $this->getDriverPinValueForDriverPin($outputPin->getDriverPin());
    }
    
    public function getDriverPinValueForDriverPin(DriverPin $pin) {
        // gets the DriverPinValue associated with the specified driver pin and this state
        
        global $infoLogger;
        
        // is there a value associated with this state?
        $driverPinValue = DriverPinValueQuery::create()->filterByState($this)->findOneByDriverPinId($pin->getId());
        
        $infoLogger->addInfo(sprintf("Looking for value associated with output pin id %d and state id %d", $pin->getId(), $this->getId()));
        
        if ($driverPinValue == null) {
            // fetch the most up-to-date state before the current one
            
            $infoLogger->addInfo("Did not find value - looking for next oldest state");
            
            $driverPinValue = DriverPinValueQuery::create()
                    ->useStateQuery()
                        ->orderByTime('desc')
                        ->filterByTime($this->getTime(), Criteria::LESS_THAN)
                    ->endUse()
                    ->findOneByDriverPinId($pin->getId());
            
            if ($driverPinValue == null) {
                $infoLogger->addInfo("Still didn't find value - this must be a new pin, so looking for the next newest state");
                
                $driverPinValue = DriverPinValueQuery::create()
                        ->useStateQuery()
                            ->orderByTime('asc')
                            ->filterByTime($this->getTime(), Criteria::GREATER_EQUAL)
                        ->endUse()
                        ->findOneByDriverPinId($pin->getId());
                
                if ($driverPinValue == null) {
                    // this shouldn't happen
                    throw new Exception(sprintf("No driver pin value could be found for driver pin id %d - something is wrong", $pin->getId()));
                }
            }
        }
        
        $infoLogger->addInfo(sprintf("Found value: %d", $driverPinValue->getValue()));
        
        return $driverPinValue;
    }
    
    public function isDeletable() {
        // check if this state is the most up-to-date for any pin        
        $mostRecentState = DriverPinValueQuery::create()->groupBy('StateId')->innerJoinState()->useStateQuery()->orderByTime('desc')->endUse()->findOne();
        
        if ($mostRecentState != null && $mostRecentState->getStateId() === $this->getId()) {
            // this state is the current state of at least one pin, so it is undeletable
            return false;
        }
        
        return true;
    }
    
    public static function getCurrentState() {
        return StateQuery::create()->orderByTime('desc')->findOne();
    }
    
    public function preDelete(ConnectionInterface $connection = null) {
        // NOTE: no delete function is needed for bookmarks or pin values, because ON DELETE = CASCADE is defined in SQL.
        
        if (! $this->isDeletable()) {
            throw new CurrentStateUndeletableException($this);
        }
        
        return true;
    }
    
    public function postDelete(ConnectionInterface $connection = null) {
        global $infoLogger;
        
        $infoLogger->addInfo(sprintf('State id %d deleted', $this->getId()));
    }
}
