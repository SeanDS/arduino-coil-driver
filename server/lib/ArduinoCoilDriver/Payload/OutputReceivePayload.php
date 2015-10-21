<?php

namespace ArduinoCoilDriver\Payload;

class OutputReceivePayload extends ReceivePayload {
    private $pinValues;

    public function __construct($content, $timeTaken) {
        parent::__construct($content, $timeTaken);
        
        // populate list of pins and values
        foreach ($this->content as $key => $value) {
            if (preg_match("/^(pin_)(\d+)$/", $key, $matches)) {
                // pin found
                $pinNumber = $matches[2];
                
                $this->pinValues[$pinNumber] = $value;
            }
        }
    }
    
    public function getMessage() {
        return sprintf("Output pin payload: %s", $this->getPinValuesString());
    }
    
    protected function getPinValuesString() {
        $entries = array();
    
        foreach ($this->getPinValues() as $pin => $value) {
            $entries[] = sprintf("[pin %d = %d]", $pin, $value);
        }
        
        return implode(", ", $entries);
    }
    
    public function getPinValues() {
        return $this->pinValues;
    }
}