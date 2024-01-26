<?php

namespace CommonBundle;

use CommonBundle\Component\Mvc\View\Http\InjectTemplateListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;
use Laminas\Stdlib\DispatchableInterface;
use Raven_ErrorHandler;

class Module
{
    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        $eventManager = $application->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        // phpcs:disable SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
        $sessionManager = $serviceManager->get(SessionManager::class);
        // phpcs:enable

        if (getenv('APPLICATION_ENV') == 'production') {
            $eventManager->attach(
                MvcEvent::EVENT_DISPATCH_ERROR,
                array(
                    $serviceManager->get('sentry_client'),
                    'logMvcEvent',
                )
            );
            $eventManager->attach(
                MvcEvent::EVENT_RENDER_ERROR,
                array(
                    $serviceManager->get('sentry_client'),
                    'logMvcEvent',
                )
            );

            $errorHandler = new Raven_ErrorHandler(
                $serviceManager->get('raven_client')
            );

            $errorHandler->registerErrorHandler()
                ->registerExceptionHandler()
                ->registerShutdownFunction();
        }

        $sharedEventManager->attach(
            DispatchableInterface::class,
            MvcEvent::EVENT_DISPATCH,
            array(
                new InjectTemplateListener(),
                'injectTemplate',
            ),
            0
        );
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        return include __DIR__ . '/Resources/config/module.config.php';
    }
}
