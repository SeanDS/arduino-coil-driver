<?php

namespace ArduinoCoilDriver\Payload;

use ArduinoCoilDriver\Exceptions\InvalidJsonException;
use ArduinoCoilDriver\Exceptions\InvalidHttpHeaderException;
use ArduinoCoilDriver\Exceptions\InvalidJsonMessageException;

abstract class ReceivePayload
{
    protected $content;
    protected $timeTaken;

    public function __construct($content, $timeTaken) {
        $this->content = $content;
        $this->timeTaken = $timeTaken;
    }
    
    public static function createFromMessage($header, $body, $timeTaken) {
        // decode JSON message
        $json = json_decode($body, true);
      
        // check for errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonException(json_last_error_msg(), $body);
        }
        
        if (array_key_exists('type', $json)) {
            // type key exists
            $type = $json['type'];
        } else {
            throw new InvalidJsonMessageException("JSON array must contain a \"type\" key", $json);
        }
    
        // extract HTTP response code
        if (! preg_match("/^HTTP\/1.1[\\s]+(\d+)/", $header, $matches)) {
            throw new InvalidHttpHeaderException($header);
        }
        
        $responseCode = $matches[1];
        
        if ($responseCode >= 200 && $responseCode < 300) {
            // OK
            
            // create payload based on type
            if ($type === "status") {
                return new StatusReceivePayload($json, $timeTaken);
            } elseif ($type === "outputs") {
                return new OutputReceivePayload($json, $timeTaken);
            } else {
                // unknown payload type
                throw new InvalidJsonMessageException("OK message type not recognised", $json);
            }
        } elseif ($responseCode >= 400 && $responseCode < 500) {
            // bad request
            if ($type === "error") {
                return new ErrorReceivePayload($json, $timeTaken);
            } else {
                // unknown payload type
                throw new InvalidJsonMessageException("Error message type not recognised", $json);
            }
        } else {
            throw new InvalidHttpHeaderException($header);
        }
    }
    
    public abstract function getMessage();
    
    public function getContent() {
        return $this->content;
    }
    
    public function getTimeTaken() {
        return $this->timeTaken;
    }
}