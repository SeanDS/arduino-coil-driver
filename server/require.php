<?php

// config settings - TODO: move somewhere else
define('ERROR_LOG_FILE', '/var/log/arduinocoildriver/error.log'); // directory must be writable by web user
define('INFO_LOG_FILE', '/var/log/arduinocoildriver/info.log'); // directory must be writable by web user
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
define('LDAP_DN', 'dc=example,dc=com'); // full bind RDN becomes uid=albert.einstein,LDAP_DN
define('LDAP_OBJECT_CLASS', 'interferometry'); // for objectClass entry within user's directory

// tank URLs, for SVG image
$tankUrls = array(
    'left_etm' => 'index.php?do=controlgroup&oid=1',
    'left_itm' => 'index.php?do=controlgroup&oid=1',
    'bottom_steering_left' => 'index.php?do=controlgroup&oid=1',
    'bottom_steering_centre' => 'index.php?do=controlgroup&oid=1',
    'middle_steering' => 'index.php?do=controlgroup&oid=1',
    'top_steering' => 'index.php?do=controlgroup&oid=1',
    'bottom_steering_right' => 'index.php?do=controlgroup&oid=1',
    'right_itm' => 'index.php?do=controlgroup&oid=1',
    'right_etm' => 'index.php?do=controlgroup&oid=1'
);

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// function (TODO: move elsewhere)
function sortValidationErrorsByProperty($obj, $namespace = "") {
    $errors = array();
    
    foreach ($obj->getValidationFailures() as $failure) {
        $errors[$namespace . $failure->getPropertyPath()][] = $failure->getMessage();
    }
    
    return $errors;
}

function formatDate($datetime) {
    return $datetime->format(DATETIME_FORMAT);
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
use Propel\Runtime\Propel;

// create error logger
$errorLogger = new Logger('defaultLogger');
$errorLogger->pushHandler(new RotatingFileHandler(ERROR_LOG_FILE, MAX_LOG_FILES, Logger::ERROR));
$errorLogger->pushProcessor(new WebProcessor());
$errorLogger->pushProcessor(new UserProcessor());

// create info logger
$infoLogger = new Logger('infoLogger');
$infoLogger->pushHandler(new RotatingFileHandler(INFO_LOG_FILE, MAX_LOG_FILES, Logger::INFO));
$infoLogger->pushProcessor(new WebProcessor());
$infoLogger->pushProcessor(new UserProcessor());

/*
 * Setup object relationship manager
 */

require_once('config.php');

// set loggers in Propel
Propel::getServiceContainer()->setLogger('defaultLogger', $errorLogger);
Propel::getServiceContainer()->setLogger('infoLogger', $infoLogger);

/*
 * Create the template engine
 */

$templates = new League\Plates\Engine(TEMPLATE_DIR);

$templates->addData(['mainTitle' => SERVER_NAME]);

/*
 * Check user credentials
 */

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