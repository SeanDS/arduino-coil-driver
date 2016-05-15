<?php

use ArduinoCoilDriver\Users\User;
use ArduinoCoilDriver\Exceptions\InvalidCredentialsException;

require('require.php');

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {    
    $get = filter_input_array(
        INPUT_GET,
        array(
            'mid'   =>  FILTER_VALIDATE_INT
        )
    );
    
    $post = filter_input_array(
        INPUT_POST,
        array(
            'username'      =>  FILTER_UNSAFE_RAW,
            'password'      =>  FILTER_UNSAFE_RAW
        )
    );
    
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
     
    echo $templates->render('login', ['messageId' => $get['mid']]);
} elseif ($do === 'logout') {
    // get user
    
    // show error if user isn't logged in
    if ($user == null) {
        $infoLogger->addInfo("User not logged in so can't be logged out");
    
        echo $templates->render('error', ['message' => 'You cannot log out if you\'re not logged in!', 'returnUrl' => "login.php"]);
        
        exit();
    }
    
    // delete session
    session_destroy();
    
    // redirect to login
    header('Location: login.php?mid=1');
}

?>