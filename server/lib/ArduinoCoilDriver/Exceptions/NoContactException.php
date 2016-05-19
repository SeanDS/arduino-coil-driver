<?php

namespace ArduinoCoilDriver\Exceptions;

use ArduinoCoilDriver\Exceptions\Base\Exception as BaseException;

class NoContactException extends BaseException
{
    public function __construct($errorCode, $errorMessage) {
        return parent::__construct(sprintf("No contact with driver - error %d: %s", $errorCode, $errorMessage));
    }
}
 
