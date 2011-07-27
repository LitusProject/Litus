<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(
	implode(PATH_SEPARATOR, array(
		realpath(APPLICATION_PATH . '/../library/zf2/library'),
    		realpath(APPLICATION_PATH . '/../library'),
    		get_include_path(),
	)
));

// Zend_Application
require_once 'Zend/Application/Application.php';

// Create application, bootstrap, and run
$application = new Zend\Application\Application (
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap('doctrine');

$em = \Zend\Registry::get(\Litus\Application\Resource\Doctrine::REGISTRY_KEY);

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));

\Doctrine\ORM\Tools\Console\ConsoleRunner::run($helperSet);
