<?php
use Phalcon\DI\FactoryDefault\CLI as CliDI,
    Phalcon\CLI\Console as ConsoleApp;
use Phalcon\Mvc\View;
define('VERSION', '1.0.0');
// Using the CLI factory default services container
$di = new CliDI();
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__)));
defined('APP_PATH') || define('APP_PATH', realpath(dirname(__FILE__)));
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/..'));
require APP_PATH . "/../vendor/autoload.php";  // including vendor folder to access .env file
$dotenv = new Dotenv\Dotenv(BASE_PATH);
$dotenv->load(); // Loading environment file
/**
* Register the autoloader and tell it to register the tasks directory
*/
$loader = new \Phalcon\Loader();
$loader->registerDirs(
    array(
        APPLICATION_PATH . '/tasks',
        APPLICATION_PATH . '/models'
    )
);
$loader->register();

if (is_readable(APPLICATION_PATH . '/config/config.php')) {
    $config = include APPLICATION_PATH . '/config/config.php';
    $di->set('config', $config);
}
// Setting the di
$config = $di->get('config');
$di->set('db', function()  {
$config = $this->getConfig(); // Accessing the DB connection from config file
    return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->dbname,
        "port" => $config->database->port
    ));
});
/**
 * Sets the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();
    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);
    return $view;
});
/**
 * Sets the mailer component
 */
$di->set('mailer', function () {
    return new \Phalcon\Ext\Mailer\Manager(
    [
        'driver'    => getenv("MAIL_DRIVER"),
        'host'       => getenv("MAIL_HOST"),
        'port'       => getenv("MAIL_PORT"),
        'encryption' => getenv("MAIL_ENC"),
        'username'   => getenv("MAIL_USER"),
        'password'   =>  getenv("MAIL_PWD"),
        'from'       => [
            'email' =>getenv("MAIL_USER"),
            'name'  => 'John Bike Service'
        ]
    ]);
});
// Create a console application
$console = new ConsoleApp();
$console->setDI($di); // pass in it the DI container.
/**
 * The first argument relates to the task to be executed. The second is the action and after that follow the parameters we need to pass.
 * Process the console arguments.
 */
$arguments = array();
$params = array();
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $params[] = $arg;
    }
}
if (count($params) > 0) {
    $arguments['params'] = $params;
}
define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));
$di->setShared("console", $console);
try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}