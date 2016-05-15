<?php

// load config
require_once('config.php');

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
 * Import Propel ORM
 */

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;

/*
 * Setup logger
 */

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\WebProcessor;
use ArduinoCoilDriver\Logger\UserProcessor;

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

$serviceContainer = Propel::getServiceContainer();
$serviceContainer->checkVersion('2.0.0-dev');
$serviceContainer->setAdapterClass('default', 'mysql');

$manager = new ConnectionManagerSingle();
$manager->setConfiguration(
    array(
        'classname' => 'Propel\\Runtime\\Connection\\DebugPDO',
        'dsn' => 'mysql:host=localhost;dbname=' . DATABASE,
        'user' => DATABASE_USER,
        'password' => DATABASE_PASSWORD,
        'attributes' => array(
            'ATTR_EMULATE_PREPARES' => false,
        ),
    )
);
$manager->setName('default');

$serviceContainer->setConnectionManager('default', $manager);
$serviceContainer->setDefaultDatasource('default');

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