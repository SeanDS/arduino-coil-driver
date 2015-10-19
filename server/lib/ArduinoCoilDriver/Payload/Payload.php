<?php

namespace ArduinoCoilDriver\Payload;

use ArduinoCoilDriver\Exceptions\InvalidJsonException;

class Payload
{
    protected $message;
    protected $timeTaken;

    public function __construct($message, $timeTaken) {
        $decodedMessage = json_decode($message, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonException(json_last_error_msg(), $message);
        }
    
        $this->message = $decodedMessage;
        $this->timeTaken = $timeTaken;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function getTimeTaken() {
        return $this->timeTaken;
    }
}