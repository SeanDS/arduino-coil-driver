<?php

// config settings - TODO: move somewhere else
define('LOG_FILE', '/var/log/arduinocoildriver/web.log'); // directory must be writable by web user
define('MAX_LOG_FILES', 10);
define('TEMPLATE_DIR', 'templates');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('SERVER_NAME', 'Arduino Coil Driver');
define('DEFAULT_SOCKET_TIMEOUT', 5); // default timeout for communication with Arduinos
define('MAXIMUM_SOCKET_LINE_LENGTH', 1024);
define('SERVER_ROOT', '/arduino-coil-driver/server/');
define('SESSION_LABEL', 'arduinocoildriver');
define('SESSION_TIME', 60 * 60 * 6);
define('LDAP_HOSTNAME', 'ldap.example.com');
define('LDAP_DN', 'dc=example,dc=com');

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// function (TODO: move elsewhere)
function sortValidationErrorsByProperty($obj) {
    $errors = array();
    
    foreach ($obj->getValidationFailures() as $failure) {
        $errors[$failure->getPropertyPath()][] = $failure->getMessage();
    }
    
    return $errors;
}

/*
 * Setup autoloader
 */

require_once('vendor/autoload.php');

/*
 * Setup logger
 */

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\WebProcessor;
use ArduinoCoilDriver\Logger\UserProcessor;

// create logging instance
$logger = new Logger('logger');
$logger->pushHandler(new RotatingFileHandler(LOG_FILE, MAX_LOG_FILES, Logger::INFO));
$logger->pushProcessor(new WebProcessor());
$logger->pushProcessor(new UserProcessor());

/*
 * Setup object relationship manager
 */

require_once('config.php');

// tell ORM about the logger
$serviceContainer->setLogger('logger', $logger);

/*
 * Create the template engine
 */
 
use Expenses\TreePlatesExtension;

$templates = new League\Plates\Engine(TEMPLATE_DIR);

$templates->addData(['mainTitle' => SERVER_NAME]);

/*
 * Check user credentials
 */

use ArduinoCoilDriver\Users\User;
use ArduinoCoilDriver\Users\UserQuery;

session_name(SESSION_LABEL);
session_set_cookie_params(SESSION_TIME, SERVER_ROOT);
session_start();

// detect user session
if (array_key_exists('userId', $_SESSION)) {
    $user = UserQuery::create()->findPK($_SESSION['userId']);
    
    if (is_null($user)) {
      throw new Exception('Specified user id does not exist');
    }
    
    $templates->addData(['user' => $user]);
} else {
    $exempt = ['login.php', 'registry.php', 'comms.php', 'test.php'];

    if (!in_array(basename($_SERVER['SCRIPT_NAME']), $exempt)) {
        header('Location: login.php');
    }
}

?>