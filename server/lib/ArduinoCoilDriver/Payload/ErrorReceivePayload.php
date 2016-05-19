<?php

namespace ArduinoCoilDriver\Payload;
use ArduinoCoilDriver\Exceptions\InvalidJsonMessageException;

class ErrorReceivePayload extends ReceivePayload {
    private $errorMessage;

    public function __construct($content, $timeTaken) {
        parent::__construct($content, $timeTaken);
        
        if (! array_key_exists('message', $this->content)) {
            throw new InvalidJsonMessageException("Error message JSON array must contain a \"message\" key", $this->content);
        }
        
        $this->errorMessage = $this->content['message'];
    }
    
    public function getMessage() {
        return sprintf("Error: %s", $this->getErrorMessage());
    }
    
    public function getErrorMessage() {
        return $this->errorMessage;
    }
}