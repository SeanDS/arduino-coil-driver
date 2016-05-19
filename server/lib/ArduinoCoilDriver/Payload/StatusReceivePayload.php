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
        $this->digital_input_1 = ($this->content['digital_input_1']);
        $this->digital_input_2 = ($this->content['digital_input_2']);
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
        return $this->digital_input_1 || $this->digital_input_2;
    }
    
    public function getCoilContactString() {
        return ($this->getCoilContact()) ? "yes" : "no";
    }
}