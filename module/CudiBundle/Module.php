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

namespace CudiBundle;

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
		$events->attach(
			'bootstrap', 'bootstrap', array($this, 'initAssetsListener')
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

	public function initAssetsListener(Event $e)
    {
        $app = $e->getParam('application');
        $this->locator = $app->getLocator();

        $app->events()->attach(
        	'dispatch', array($this, 'renderAssets')
        );
    }

    public function renderAssets(MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        $router = $e->getRouteMatch();

        $as = $this->locator->get('assetic');

        $as->setRouteName($router->getMatchedRouteName());
        $as->setControllerName($router->getParam('controller'));
        $as->setActionName($router->getParam('action'));

        $as->initLoadedModules($this->moduleManager->getLoadedModules());
        $as->setupViewHelpers($this->locator->get('view'));
    }

    public function initializeView(Event $e)
    {
        $app = $e->getParam('application');
        $locator = $app->getLocator();
        $view = $locator->get('view');
		
		$view->getEnvironment()->getLoader()->addPath(__DIR__ . '/../CommonBundle/src/Resources/layouts');
        $view->getEnvironment()->getLoader()->addPath(__DIR__ . '/src/Resources/views');

        $url = $view->plugin('url');
        $url->setRouter($app->getRouter());
        
        $view->plugin('doctype')->setDoctype(Doctype::HTML5);
        $view->plugin('headTitle')->setSeparator('&mdash;');
    }
    
    public function getProvides()
    {
        return array(
            'name'    => 'commonbundle',
            'version' => '1.0.0',
        );
    }
}