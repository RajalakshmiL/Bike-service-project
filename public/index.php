<?php
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {
      /**
       * The FactoryDefault Dependency Injector automatically registers the services that
       * provide a full stack framework. These default services can be overidden with custom ones.
      */
      $di = new FactoryDefault();
      /**
       * Include Services
       */
      include APP_PATH . '/config/services.php';
      /**
       * Get config service for use in inline setup below
       */
      $config = $di->getConfig();
      /**
       * Include Autoloader
       */
      include APP_PATH . '/config/loader.php';
      /**
       * Starting the application
       * Assign service locator to the application
       */
      $app = new Micro($di);
      /*
      check for success response
      */
      $app->options('/{catch:(.*)}', function() use ($app) { 
              $app->response->setStatusCode(200, "OK")->send();
      });
      /**
       * Include Application
       */
      include APP_PATH . '/app.php';
      /**
       * Handle the request
       */
      $app->handle();

  } catch (\Exception $e) {
      echo $e->getMessage() . '<br>';
      echo '<pre>' . $e->getTraceAsString() . '</pre>';
      /**
       *   Logging the error in .txt file
       **/
      $logger = new FileAdapter(APP_PATH . "/logs/log.log"); // Create the logger
      // Start a transaction
      $logger->begin();
      // Log messages
      $logger->error(
        $e->getMessage(). '<br>' . $e->getFile(). '<br>' . $e->getLine()
      );
      // Commit messages to file
      $logger->commit();
  }