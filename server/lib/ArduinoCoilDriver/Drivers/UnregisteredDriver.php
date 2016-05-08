<?php

namespace ArduinoCoilDriver\Drivers;

use ArduinoCoilDriver\Drivers\Base\UnregisteredDriver as BaseUnregisteredDriver;
use ArduinoCoilDriver\Payload\ReceivePayload;
use ArduinoCoilDriver\Payload\StatusReceivePayload;
use ArduinoCoilDriver\Payload\OutputReceivePayload;

/**
 * Skeleton subclass for representing a row from the 'drivers_unregistered' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class UnregisteredDriver extends BaseUnregisteredDriver
{
    private function contact($get) {
        global $logger;
        
        $logger->addInfo(sprintf('Contacting unregistered driver id %d: %s', $this->getId(), $get));
        
        // return payload
        return ReceivePayload::payloadFromGet($this->getIp(), 80, $get);
    }

    public function getStatus() {
        global $logger;
        
        $logger->addInfo(sprintf('Getting status of unregistered driver id %d', $this->getId()));
        
        $payload = $this->contact("/status");
        
        if (! $payload instanceof StatusReceivePayload) {
            throw new Exception("Received payload is not a StatusReceivePayload");
        }
        
        return $payload;
    }
    
    public function getOutputs() {
        global $logger;
        
        $logger->addInfo(sprintf('Getting outputs of unregistered driver id %d', $this->getId()));
        
        $payload = $this->contact("/outputs");
        
        if (! $payload instanceof OutputReceivePayload) {
            throw new Exception("Received payload is not a OutputReceivePayload");
        }
        
        return $payload;
    }
}
