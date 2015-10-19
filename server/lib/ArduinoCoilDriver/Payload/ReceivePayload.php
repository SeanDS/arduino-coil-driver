<?php

namespace ArduinoCoilDriver\Payload;

class OutputPayload extends Payload {
    private $pinValues;

    public function __construct($message, $timeTaken) {
        parent::__construct($message, $timeTaken);
        
        // populate list of pins and values
        foreach ($this->message as $key => $value) {
            if (preg_match("/^(pin_)(\d+)$/", $key, $matches)) {
                // pin found
                $pinNumber = $matches[2];
                
                $this->pinValues[$pinNumber] = $value;
            }
        }
    }
    
    public function getPinValues() {
        return $this->pinValues;
    }
}