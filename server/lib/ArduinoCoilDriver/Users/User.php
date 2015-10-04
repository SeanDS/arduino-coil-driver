<?php

namespace ArduinoCoilDriver\Users;

use ArduinoCoilDriver\Users\Base\User as BaseUser;

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
        } catch (BindingException $e) {
            throw new InvalidCredentialsException($e);
        }
        
        return UserQuery::create()->findOneByName($username);
    }
}
