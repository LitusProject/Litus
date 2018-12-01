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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle;

use CommonBundle\Component\Mvc\View\Http\InjectTemplateListener;
use Raven_ErrorHandler;
use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;
use Zend\Stdlib\DispatchableInterface;

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
                    'logMvcEvent'
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
                'injectTemplate'
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
