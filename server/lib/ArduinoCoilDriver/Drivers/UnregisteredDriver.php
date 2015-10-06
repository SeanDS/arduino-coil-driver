<?php

namespace ArduinoCoilDriver\Drivers;

use ArduinoCoilDriver\Drivers\Base\UnregisteredDriver as BaseUnregisteredDriver;
use ArduinoCoilDriver\Exceptions\NoContactException;
use ArduinoCoilDriver\Payload\StatusPayload;
use ArduinoCoilDriver\Payload\OutputPayload;

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
    private function contact($path) {
        // start clock
        $startTime = microtime(true);
        
        // open socket
        $socket = @fsockopen($this->getIp(), 80, $errorCode, $errorString, DEFAULT_SOCKET_TIMEOUT);
        
        // if socket isn't open
        if (! $socket) {            
            throw new NoContactException($errorCode, $errorString);
        }
        
        // ask for status
        fwrite($socket, "GET " . $path . " HTTP/1.1\r\nHOST: " . $this->getIp() . "\r\n\r\n");

        $message = "";
        
        $contentFlag = false;
        
        // compile returned message
        while (! feof($socket)) {
            $line = fgets($socket, MAXIMUM_SOCKET_LINE_LENGTH);
            
            if ($contentFlag) {
                $message .= $line;
            } else {
                if ($line == "\r\n" && !$contentFlag) {
                    // this is the last line
                    $contentFlag = true;
                }
            }
        }
        
        // close socket
        fclose($socket);
        
        // stop clock
        $endTime = microtime(true);
        
        return $message;
    }

    public function getStatus() {
        $startTime = microtime(true);
        $message = $this->contact("/status");
        $endTime = microtime(true);
        
        return new StatusPayload($message, $endTime - $startTime);
    }
    
    public function getOutputs() {
        $startTime = microtime(true);
        $message = $this->contact("/outputs");
        $endTime = microtime(true);
        
        return new OutputPayload($message, $endTime - $startTime);
    }
}
