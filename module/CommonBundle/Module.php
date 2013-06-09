<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle;

use CommonBundle\Component\Mvc\View\Http\InjectTemplateListener,
    Zend\Mvc\ModuleRouteListener,
    Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap($event)
    {
        $application  = $event->getApplication();
        $services     = $application->getServiceManager();
        $events       = $application->getEventManager();
        $sharedEvents = $events->getSharedManager();

        /*
        if ('production' == getenv('APPLICATION_ENV'))
            $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($services->get('lilo'), 'handleMvcEvent'));
        */

        $injectTemplateListener = new InjectTemplateListener();
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($injectTemplateListener, 'injectTemplate'), 0);

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($events);
    }

    public function getConfig()
    {
        return include __DIR__ . '/Resources/config/module.config.php';
    }
}
