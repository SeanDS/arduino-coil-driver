<?php

namespace ArduinoCoilDriver\Exceptions;

use ArduinoCoilDriver\Exceptions\Base\Exception as BaseException;

class InvalidJsonException extends BaseException
{
    public function __construct($error, $json) {
        return parent::__construct(sprintf("Invalid JSON: %s. Original JSON string: %s", $error, $json));
    }
}
 
