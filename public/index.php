<?php

if ('development' == getenv('APPLICATION_ENV')) {
    ini_set('display_errors', true);
    error_reporting(E_ALL);
}

set_error_handler(
    function ($errorNb, $errorString, $errorFile, $errorLine ) {
        throw new ErrorException($errorString, $errorNb, 0, $errorFile, $errorLine);
    }
);

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
include 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(include 'config/application.config.php')->run();