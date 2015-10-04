<?php

require('require.php');

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'message'    =>  FILTER_SANITIZE_STRING
        )
    );
    
    echo $templates->render('dashboard');
}

?>