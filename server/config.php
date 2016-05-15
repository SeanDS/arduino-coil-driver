<?php

// debug mode
define('DEBUG', true);

// database settings
define('DATABASE', 'arduinocoildriver');
define('DATABASE_USER', 'jif');
define('DATABASE_PASSWORD', 'Y9GA98FrTAPjb5QV');

// log settings
define('ERROR_LOG_FILE', '/var/log/arduinocoildriver/error.log'); // directory must be writable by web user
define('INFO_LOG_FILE', '/var/log/arduinocoildriver/info.log'); // directory must be writable by web user
define('MAX_LOG_FILES', 10);

// template settings
define('TEMPLATE_DIR', 'templates');

// server settings
define('SERVER_NAME', 'Arduino Coil Driver');
define('SERVER_ROOT', '/arduino-coil-driver/server/');
define('SESSION_LABEL', 'arduinocoildriver');
define('SESSION_TIME', 60 * 60 * 6);
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// communication settings
define('DEFAULT_SOCKET_TIMEOUT', 5); // default timeout for communication with Arduinos
define('MAXIMUM_SOCKET_LINE_LENGTH', 1024);

// LDAP settings
define('LDAP_HOSTNAME', 'ldap.example.com');
define('LDAP_DN', 'dc=example,dc=com'); // full bind RDN becomes uid=albert.einstein,LDAP_DN
define('LDAP_SEARCH_DN', 'cn=groupname,ou=Groups,dc=example,dc=com');
define('LDAP_SEARCH_FILTER', '(uniqueMember=*)');
define('LDAP_SEARCH_ATTRIBUTES', 'uniquemember');

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

?>