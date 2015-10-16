<?php

namespace ArduinoCoilDriver\States;

use ArduinoCoilDriver\States\Base\State as BaseState;

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
}
