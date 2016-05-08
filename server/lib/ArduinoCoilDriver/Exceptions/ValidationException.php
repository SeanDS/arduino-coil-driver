<?php

namespace ArduinoCoilDriver\Exceptions;

use ArduinoCoilDriver\Exceptions\Base\Exception as BaseException;

class ValidationException extends BaseException
{
    protected $errors;

    public function __construct($object, $namespace = "") {
        // namespace is appended to property path
        
        $this->errors = sortValidationErrorsByProperty($object, $namespace);
    
        return parent::__construct();
    }
    
    public function getErrors() {
        return $this->errors;
    }
}
 
