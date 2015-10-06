<?php

namespace ArduinoCoilDriver\Payload;

class StatusPayload extends Payload {
    private $sdCard;
    private $mac;
    private $ip;
    private $version;
    private $digital_input_1;
    private $digital_input_2;

    public function __construct($message, $timeTaken) {
        parent::__construct($message, $timeTaken);
        
        $this->sdCard = ($this->message['sdcard'] === 'present');
        
        // convert MAC from base 10 to base 16
        $pieces = explode(':', $this->message['mac']);
        
        foreach ($pieces as $key => $piece) {
            $pieces[$key] = base_convert($piece, 10, 16);
            
            if (strlen($pieces[$key]) < 2) {
                // piece is a singular digit
                // prepend a zero
                $pieces[$key] = "0" . $pieces[$key];
            }
        }
        
        $this->mac = implode(':', $pieces);
        $this->ip = $this->message['ip'];
        $this->version = $this->message['version'];
        $this->digital_input_1 = ($this->message['digital_input_1']);
        $this->digital_input_2 = ($this->message['digital_input_2']);
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
}