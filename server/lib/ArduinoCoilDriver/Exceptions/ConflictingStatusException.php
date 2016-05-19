<?php

namespace ArduinoCoilDriver\Exceptions;

use ArduinoCoilDriver\Exceptions\Base\Exception as BaseException;
use ArduinoCoilDriver\Drivers\UnregisteredDriver;

class ConflictingStatusException extends BaseException
{
    public function __construct(UnregisteredDriver $unregisteredDriver) {
        return parent::__construct(sprintf("Driver with MAC %s and IP %s has reported a conflicting status from the record.", $unregisteredDriver->getMac(), $unregisteredDriver->getIp()));
    }
}
 
