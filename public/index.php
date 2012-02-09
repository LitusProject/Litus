<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

if ('development' == getenv('APPLICATION_ENV')) {
	ini_set('display_errors', true);
	error_reporting(E_ALL);
}
	 
chdir(dirname(__DIR__));

require_once (getenv('ZF2_PATH') ?: 'vendor/ZendFramework/library') . '/Zend/Loader/AutoloaderFactory.php';

Zend\Loader\AutoloaderFactory::factory(
	array(
		'Zend\Loader\StandardAutoloader' => array()
	)
);

$appConfig = include 'config/application.config.php';

$listenerOptions = new Zend\Module\Listener\ListenerOptions(
	$appConfig['module_listener_options']
);
$defaultListeners = new Zend\Module\Listener\DefaultListenerAggregate(
	$listenerOptions
);
$defaultListeners->getConfigListener()
	->addConfigGlobPath('config/autoload/*.config.php');

$moduleManager = new Zend\Module\Manager(
	$appConfig['modules']
);
$moduleManager->events()
	->attachAggregate($defaultListeners);
$moduleManager->loadModules();

// Create application, bootstrap, and run
$bootstrap = new Zend\Mvc\Bootstrap(
	$defaultListeners->getConfigListener()->getMergedConfig()
);
$application = new Zend\Mvc\Application;
$bootstrap->bootstrap(
	$application
);
$application->run()->send();
