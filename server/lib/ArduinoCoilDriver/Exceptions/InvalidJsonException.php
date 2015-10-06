<?php

namespace ArduinoCoilDriver\Exceptions;

use ArduinoCoilDriver\Exceptions\Base\Exception as BaseException;

class InvalidJsonException extends BaseException
{
    public function __construct($error) {
        return parent::__construct(sprintf("Invalid JSON: %s", $error));
    }
}
 
