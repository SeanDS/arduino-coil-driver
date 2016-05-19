<?php

namespace ArduinoCoilDriver\Exceptions;

use ArduinoCoilDriver\Exceptions\Base\Exception as BaseException;
use ArduinoCoilDriver\States\State;

class LatestStateAlreadyLoadedException extends BaseException
{
    public function __construct(State $state) {
        return parent::__construct(sprintf("State id %d is already loaded", $state->getId()));
    }
}
 
