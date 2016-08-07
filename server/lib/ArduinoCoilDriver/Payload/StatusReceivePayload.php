<?php

namespace ArduinoCoilDriver\Payload;

class StatusReceivePayload extends ReceivePayload {
    private $sdCard;
    private $mac;
    private $ip;
    private $version;
    private $digital_input_1;
    private $digital_input_2;

    public function __construct($content, $timeTaken) {
        parent::__construct($content, $timeTaken);
        
        $this->sdCard = ($this->content['sdcard'] === 'present');
        
        // convert MAC from base 10 to base 16
        $pieces = explode(':', $this->content['mac']);
        
        foreach ($pieces as $key => $piece) {
            $pieces[$key] = base_convert($piece, 10, 16);
            
            if (strlen($pieces[$key]) < 2) {
                // piece is a singular digit
                // prepend a zero
                $pieces[$key] = "0" . $pieces[$key];
            }
        }
        
        $this->mac = implode(':', $pieces);
        $this->ip = $this->content['ip'];
        $this->version = $this->content['version'];
        $this->digital_input_1 = intval($this->content['digital_input_1']);
        $this->digital_input_2 = intval($this->content['digital_input_2']);
    }
    
    public function getMessage() {
        return sprintf("Status payload: %s", $this->getStatusString());
    }
    
    protected function getStatusString() {
        return sprintf("[sdcard = %s], [mac = %s], [ip = %s], [version = %s], [coil contact = %s]", $this->getSdCardString(), $this->getMac(), $this->getIp(), $this->getVersion(), $this->getCoilContactString());
    }
    
    public function getSdCard() {
        return $this->sdCard;
    }
    
    public function getSdCardString() {
        return ($this->getSdCard()) ? "yes" : "no";
    }
    
    public function getMac() {
        return $this->mac;
    }
    
    public function getIp() {
        return $this->ip;
    }
    
    public function getVersion() {
        return $this->version;
    }
    
    public function getCoilContact() {
        // There are two digital pins used to sense coil contact.
        // These should be pulled up, i.e. the normal, no coil contact
        // situation is 1. When coil contact occurs the pin should read
        // 0. Therefore the overall coil contact flag (1 = yes, 0 = no)
        // is set if either input is 0, i.e. NAND
        return ! ($this->digital_input_1 && $this->digital_input_2);
    }
    
    public function getCoilContactString() {
        return ($this->getCoilContact()) ? "yes" : "no";
    }
    
    public function getFirstCoilContact() {
        // Coil is contacting if the input is not 1
        return ! $this->digital_input_1;
    }
    
    public function getFirstCoilContactString() {
        return ($this->getFirstCoilContact()) ? "yes" : "no";
    }
    
    public function getSecondCoilContact() {
        // Coil is contacting if the input is not 1
        return ! $this->digital_input_2;
    }
    
    public function getSecondCoilContactString() {
        return ($this->getSecondCoilContact()) ? "yes" : "no";
    }
}