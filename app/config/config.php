<?php
/*
 * It includes the configurations for database connectivity as per the directory path.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');
require APP_PATH . "/../vendor/autoload.php"; // including vendor folder for Dotenv file
$dotenv = new Dotenv\Dotenv(BASE_PATH);
$dotenv->load();
return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => getenv('DBHOST_ADMDB'),
        'username'    => getenv('DBUSER_ADMDB'),
        'password'    => getenv('DBPWD_ADMDB'),
        'dbname'      => getenv('DBNAME_ADMDB'),
        'port'      => getenv('DBPORT_ADMDB'),
        'charset'     => 'utf8',
    ],
    'application' => [
        'modelsDir'      => APP_PATH . '/models/',
        'libraryDir'       => APP_PATH . '/library/',
        'viewsDir'       => APP_PATH . '/views/',
        'controllersDir'       => APP_PATH . '/controllers/',
        'incubatorDir'       => APP_PATH . '/incubator/Phalcon/',
        'baseUri'        => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
    ],
]);