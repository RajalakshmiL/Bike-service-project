<?php
use Phalcon\Session\Adapter\Database;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Acc\Auth\Auth;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});
/*Set auth file for verifying the login and registration user */
$di->set('auth', function () {
    return new Auth();
});
/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();
    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset,
        'port'  =>  $config->database->port
    ];
    $connection = new $class($params);

    return $connection;
});
/**
 * Session is created based in the parameters defined in the configuration file
 */
$di->set('session', function() {       
    $config = $this->getConfig();
    $connection = new  Mysql([
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset,
        'port'  =>  $config->database->port
    ]);
    // Set the max lifetime of a session with 'ini_set()' to one hour
    session_set_cookie_params(3600);
    $session = new Database([
        'db'    => $connection,
        'table' => 'session_data'
    ]);
    $session->setId($_COOKIE["PHPSESSID"]);
    $session->start();
    return $session;
});
