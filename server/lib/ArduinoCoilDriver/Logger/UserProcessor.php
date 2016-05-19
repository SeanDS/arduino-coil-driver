<?php

namespace ArduinoCoilDriver\Logger;

use ArduinoCoilDriver\Users\User;

class UserProcessor
{    
    public function __construct()
    {
    
    }
    
    public function __invoke(array $record)
    {
        global $user;
        
        // if $user doesn't exist, don't do anything
        if (! isset($user) || !($user instanceof User)) {
            return $record;
        }
        
        // otherwise, add the user info
        $record['extra'] = $this->appendExtraFields($record['extra']);
        
        return $record;
    }
    
    private function appendExtraFields(array $extra)
    {
        global $user;
    
        $extra['user'] = $user->getName();
        
        return $extra;
    }
}