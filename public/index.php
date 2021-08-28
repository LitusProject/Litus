<?php

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

if (getenv('APPLICATION_ENV') === false) {
    putenv('APPLICATION_ENV=development');
}

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

if (!class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
        . "- Type `docker-compose run zf composer install` if you are using Docker.\n"
    );
}

// Our old HAProxy configuration added a prefix to the PHP_SESSID cookie
if (isset($_COOKIE[session_name()])) {
    if (preg_match('/^[a-zA-Z0-9-,]{1,128}$/', $_COOKIE[session_name()]) === 0) {
        setcookie(session_name(), '', (time() - 3600));
    }
}

// Retrieve configuration
$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/../config/development.config.php');
}

// Run the application!
Application::init($appConfig)->run();
