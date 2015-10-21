<?php

namespace ArduinoCoilDriver\Exceptions;

use ArduinoCoilDriver\Exceptions\Base\Exception as BaseException;

class InvalidJsonMessageException extends BaseException
{
    public function __construct($message, $jsonArray) {
        return parent::__construct(sprintf("%s. JSON array: %s", $message, json_encode($jsonArray)));
    }
}