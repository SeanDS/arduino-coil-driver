<?php

namespace ArduinoCoilDriver\States;

use Propel\Runtime\Propel;
use ArduinoCoilDriver\States\Base\State as BaseState;
use ArduinoCoilDriver\Drivers\DriverPinValueQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Exceptions\CurrentStateUndeletableException;

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
    
    public function isDeletable() {
        // check if this state is the most up-to-date for any pin        
        $mostRecentState = DriverPinValueQuery::create()->groupBy('StateId')->innerJoinState()->useStateQuery()->orderByTime('desc')->endUse()->findOne();
        
        if ($mostRecentState != null && $mostRecentState->getStateId() === $this->getId()) {
            // this state is the current state of at least one pin, so it is undeletable
            return false;
        }
        
        return true;
    }
    
    public function preDelete(ConnectionInterface $connection = null) {
        // NOTE: no delete function is needed for bookmarks or pin values, because ON DELETE = CASCADE is defined in SQL.
        
        if (! $this->isDeletable()) {
            throw new CurrentStateUndeletableException($this);
        }
        
        return true;
    }
    
    public function postDelete(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('State id %d deleted', $this->getId()));
    }
}
