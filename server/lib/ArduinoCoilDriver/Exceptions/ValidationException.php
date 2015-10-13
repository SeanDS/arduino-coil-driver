<?php

namespace ArduinoCoilDriver\Exceptions;

use ArduinoCoilDriver\Exceptions\Base\Exception as BaseException;

class ValidationException extends BaseException
{
    protected $errors;

    public function __construct($object) {
        $this->errors = sortValidationErrorsByProperty($object);
    
        return parent::__construct();
    }
    
    public function getErrors() {
        return $this->errors;
    }
}
 
