<?php

use ArduinoCoilDriver\Users\User;
use ArduinoCoilDriver\Users\UserQuery;
use ArduinoCoilDriver\Exceptions\InvalidCredentialsException;

require('require.php');

$post = filter_input_array(
    INPUT_POST,
    array(
        'username'      =>  FILTER_UNSAFE_RAW,
        'password'      =>  FILTER_UNSAFE_RAW
    )
);

if (empty($get['do'])) {
    if (!empty($post['username']) && !empty($post['password'])) {
        // check submitted credentials
        try {
            $user = User::login($post['username'], $post['password']);
            
            // set session
            $_SESSION['userId'] = $user->getId();
            
            // redirect user
            header('Location: index.php');
            
            // end script
            exit();
        } catch (InvalidCredentialsException $e) {
            // set error in template
            $templates->addData(['badCredentials' => true], ['login']);
        }
    }
    
    /*
     * show login screen
     */
     
    echo $templates->render('login');
}
?>