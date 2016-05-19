<?php

namespace ArduinoCoilDriver\Exceptions;

use ArduinoCoilDriver\Exceptions\Base\Exception as BaseException;
use ArduinoCoilDriver\States\State;

class CurrentStateUndeletableException extends BaseException
{
    public function __construct(State $state) {
        return parent::__construct(sprintf("State id %i cannot be deleted because it is the most up to date state for one or more pins.", $state->getId()));
    }
}
 
