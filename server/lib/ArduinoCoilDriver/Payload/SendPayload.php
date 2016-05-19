<?php

namespace ArduinoCoilDriver\Payload;

use ArduinoCoilDriver\Exceptions\InvalidJsonException;

class SendPayload
{
    protected $path;
    protected $message;

    public function __construct($path, $message) {
        $message = json_encode($message, JSON_NUMERIC_CHECK);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonException(json_last_error_msg());
        }
    
        $this->path = $path;
        $this->message = $message;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function getRequest() {
        return $this->path . "?set=" . $this->message;
    }
}