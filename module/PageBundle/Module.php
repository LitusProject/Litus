<?php

namespace PageBundle;

use Zend\Module\Manager,
	Zend\EventManager\Event,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\Mvc\MvcEvent,
    Zend\View\Helper\Doctype;

class Module implements AutoloaderProvider
{
	protected $locator = null;
	protected $moduleManager = null;

	public function init(Manager $moduleManager)
    {
    	$this->moduleManager = $moduleManager;
    
		$events = StaticEventManager::getInstance();
		$events->attach(
			'bootstrap', 'bootstrap', array($this, 'initializeView')
		);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ 	=> __DIR__ . '/src/' . __NAMESPACE__,
                )
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/src/Resources/config/module.config.php';
    }

    public function initializeView(Event $e)
    {
        $app = $e->getParam('application');
        $locator = $app->getLocator();
        $view = $locator->get('view');
		
        $view->getEnvironment()->getLoader()->addPath(__DIR__ . '/src/Resources/views');
    }
    
    public function getProvides()
    {
        return array(
            'name'    => 'PageBundle',
            'version' => '1.0.0',
        );
    }
}