<?php

namespace ArduinoCoilDriver\Users;

use ArduinoCoilDriver\Users\Base\User as BaseUser;
use ArduinoCoilDriver\Exceptions\InvalidCredentialsException;

/**
 * Skeleton subclass for representing a row from the 'users' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class User extends BaseUser
{
    public static function login($username, $password) {    
        $connection = ldap_connect(LDAP_HOSTNAME, LDAP_PORT);

        // bind, checking credentials
        if (! ldap_bind($connection, 'uid=' . $username . ',' . LDAP_DN, $password)) {
            throw new InvalidCredentialsException();
        }
        
        // default found
        $found = false;
        
        // I apologise for the horribleness of the code below. There's
        // something horrifically wrong with either the LDAP server
        // I use, or LDAP in general (probably both) that makes this
        // search routine so weird.
        
        // the user exists, but are they valid?
        if (LDAP_SEARCH_FILTER != null) {
            $search = ldap_search($connection, LDAP_SEARCH_DN, LDAP_SEARCH_FILTER, array('uniquemember'));
            
            $entries = ldap_get_entries($connection, $search);
            
            foreach ($entries[0][LDAP_SEARCH_ATTRIBUTES] as $member) {
                if (substr($member, 0, 4) === 'uid=') {
                    if (substr($member, 4) === $username . "," . LDAP_DN) {
                        $found = true;
                    }
                }
            }
        } else {
            $found = true;
        }
        
        if (! $found) {
            throw new InvalidCredentialsException();
        }
        
        $user = UserQuery::create()->findOneByName($username);
        
        if ($user == null) {
            // user hasn't been created yet
            $user = new self();
            $user->setName($username);
            $user->setFirstLogin(time());
            $user->save();
        }
        
        $user->setLastLogin(time());
        $user->save();
        
        return $user;
    }
}
