<?php

namespace ArduinoCoilDriver\Users;

use ArduinoCoilDriver\Users\Base\User as BaseUser;
use Toyota\Component\Ldap\Core\Manager;
use Toyota\Component\Ldap\Platform\Native\Driver;
use Toyota\Component\Ldap\Exception\BindException;
use Toyota\Component\Ldap\Platform\Native\Search;
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
        return UserQuery::create()->findOneByName('sean.leavey');
    
        $params = array(
            'hostname'      => LDAP_HOSTNAME,
            'base_dn'       => LDAP_DN
        );
        
        // create LDAP connection
        $manager = new Manager($params, new Driver());

        // connect
        $manager->connect();

        // bind, checking credentials
        try {
            $manager->bind('uid=' . $username . ',' . LDAP_DN, $password);
        } catch (BindException $e) {
            throw new InvalidCredentialsException($e);
        }
        
        // the user exists, but are they valid?
        if (LDAP_SEARCH_FILTER != null) {
            $search = $manager->search(Search::SCOPE_BASE, LDAP_DN, LDAP_SEARCH_FILTER);
            
            $entry = $search->next();
            
            if ($entry == null) {
                throw new InvalidCredentialsException();
            }
            
            $attributes = $entry->getAttributes();
            
            // check user has correct class
            if (! array_key_exists('objectClass', $attributes) || ! in_array(LDAP_OBJECT_CLASS, $attributes)) {
                throw new InvalidCredentialsException();
            }
        }
        
        // TODO: set firstlogin and lastlogin
        
        return UserQuery::create()->findOneByName($username);
    }
}
