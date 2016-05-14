<?php

namespace ArduinoCoilDriver\States;

use DateTime;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Drivers\DriverQuery;
use ArduinoCoilDriver\Drivers\DriverOutputPin;
use ArduinoCoilDriver\Drivers\DriverPinValueQuery;
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
    
        $state = new self();
        
        $state->setUserId($user->getId());
        $state->setTime('now');
        $state->save();
        
        return $state;
    }
    
    public function load() {
        // loads this state
        
        // check if this state is the latest (we don't need to load)
        if ($this == self::getCurrentState()) {
            throw new LatestStateAlreadyLoadedException($this);
        }
        
        // take a copy of the current state
        $currentState = clone $this;
        
        // get drivers
        $drivers = DriverQuery::create()->find();
        
        try {
            // snap each driver to this state, or nearest before
            foreach ($drivers as $driver) {
                $driver->snapToState($this);
            }
            
            // update this state's time
            $this->setTime(new DateTime());
            
            // save
            $this->save();
        } catch (Exception $e) {
            // reset to current state
            foreach ($drivers as $driver) {
                $driver->snapToState($currentState);
            }
            
            // throw the exception
            throw $e;
        }
    }
    
    public function getValuesForDriverOutputPins(Array $outputs) {
        // gets the values associated with the specified output pins and this state
        
        // array to hold values
        $values = array();
        
        foreach ($outputs as $key => $output) {
            $values[$key] = $this->getValueForDriverOutputPin($output);
        }
        
        return $values;
    }
    
    public function getValueForDriverOutputPin(DriverOutputPin $pin) {
        // gets the value associated with the specified output pin and this state
        
        global $infoLogger;
        
        // is there a value associated with this state?
        $outputPinValue = DriverPinValueQuery::create()->filterByState($this)->findOneByDriverPinId($pin->getId());
        
        $infoLogger->addInfo(sprintf("Looking for value associated with output pin id %d and state id %d", $pin->getId(), $this->getId()));
        
        if ($outputPinValue == null) {
            // fetch the most up-to-date state before the current one
            
            $infoLogger->addInfo("Did not find value - looking for next oldest state");
            
            $outputPinValue = DriverPinValueQuery::create()
                    ->innerJoinState()
                    ->useStateQuery()
                        ->orderByTime('desc')
                        ->filterByTime($this->getTime(), Criteria::LESS_THAN)
                    ->endUse()
                    ->findOneById($pin->getId());
            
            if ($outputPinValue == null) {
                throw new Exception("Pin value not found - this should not happen (a state that contains this pin's value has been deleted)");
            }
        }
        
        $infoLogger->addInfo(sprintf("Found value: %d", $outputPinValue->getValue()));
            
        return $outputPinValue->getValue();
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
