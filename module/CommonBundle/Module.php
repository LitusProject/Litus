<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle;

use CommonBundle\Component\Mvc\View\Http\InjectTemplateListener,
    Zend\Mvc\MvcEvent,
    Zend\Console\Request as ConsoleRequest,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\ServiceManager,
    Symfony\Component\Console\Application as ConsoleApplication;

class Module
{
    public function onBootstrap(MvcEvent $event)
    {
        $application  = $event->getApplication();
        $services     = $application->getServiceManager();
        $events       = $application->getEventManager();
        $sharedEvents = $events->getSharedManager();

        if ('production' == getenv('APPLICATION_ENV'))
            $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($services->get('lilo'), 'handleMvcEvent'));

        $injectTemplateListener = new InjectTemplateListener();
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($injectTemplateListener, 'injectTemplate'), 0);

        if ($event->getRequest() instanceof ConsoleRequest) {
            $event->setRouter($services->get('litus.console_router'));
            $this->initializeConsole($services->get('litus.console_application'), $services);
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/Resources/config/module.config.php';
    }

    /**
     * Adds the console routes to the $application.
     *
     * @param  ConsoleApplication      $application    the console application
     * @param  ServiceLocatorInterface $serviceLocator the ZF2 service locator
     * @return void
     */
    public function initializeConsole(ConsoleApplication $application, ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = $config['litus']['console'];

        $commands = array();

        // Use the $serviceLocator here because it injects dependencies of the instantiated classes.
        // Added bonus: allows commands to be overriden!
        if ($serviceLocator instanceof ServiceManager) {
            foreach ($config as $name => $invokable) {
                $serviceLocator->setInvokableClass('litus.console.' . $name, $invokable);
                $commands[$name] = $serviceLocator->get('litus.console.' . $name);
            }
        }

        $application->addCommands(array_values($commands));
    }
}
