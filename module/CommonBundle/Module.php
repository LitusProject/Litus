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

use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface,
    Zend\ModuleManager\Feature\ConsoleBannerProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    CommonBundle\Component\Mvc\View\Http\InjectTemplateListener,
    Zend\Mvc\MvcEvent,
    Zend\Console\Adapter\AdapterInterface as Console;

class Module implements ConfigProviderInterface, ConsoleUsageProviderInterface, ConsoleBannerProviderInterface
{
    public function onBootstrap($event)
    {
        $application  = $event->getApplication();
        $services     = $application->getServiceManager();
        $events       = $application->getEventManager();
        $sharedEvents = $events->getSharedManager();

        if ('development' != getenv('APPLICATION_ENV'))
            $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($services->get('lilo'), 'handleMvcEvent'));

        $injectTemplateListener = new InjectTemplateListener();
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($injectTemplateListener, 'injectTemplate'), 0);
    }

    public function getConfig()
    {
        return include __DIR__ . '/Resources/config/module.config.php';
    }

    public function getConsoleBanner(Console $console)
    {
        return 'Litus';
    }

    public function getConsoleUsage(Console $console)
    {
        return array(
            '' // TODO
        );
    }
}
