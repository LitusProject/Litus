<?php

namespace CudiBundle;

use Zend\Module\Manager,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider;

class Module implements AutoloaderProvider
{
	public function init(Manager $moduleManager)
    {
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

    public function initializeView(\Zend\EventManager\Event $e)
    {
        $app = $e->getParam('application');
        $locator = $app->getLocator();
        $view = $locator->get('view');
        
        $view->getEnvironment()->getLoader()->addPath(__DIR__ . '/../CommonBundle/src/Resources/views');
        $view->getEnvironment()->getLoader()->addPath(__DIR__ . '/src/Resources/views');

        $url = $view->plugin('url');
        $url->setRouter($app->getRouter());

        $view->plugin('headTitle')->setSeparator(' - ')
        	->setAutoEscape(false)
            ->set('Litus Admin');
    }
    
    public function getProvides()
    {
        return array(
            'name'    => 'cudibundle',
            'version' => '1.0.0',
        );
    }
}