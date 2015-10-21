<?php

namespace ArduinoCoilDriver\Exceptions;

use ArduinoCoilDriver\Exceptions\Base\Exception as BaseException;

class InvalidHttpHeaderException extends BaseException
{
    public function __construct($error) {
        return parent::__construct(sprintf("HTTP header cannot be handled: %s", $header));
    }
}