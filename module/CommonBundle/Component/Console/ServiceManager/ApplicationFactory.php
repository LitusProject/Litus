<?php

namespace CommonBundle\Component\Console\ServiceManager;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\Version\Version;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Raven_ErrorHandler;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Factory to instantiate a console application.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ApplicationFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Application
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $application = new Application('Litus', Version::getShortCommitHash());
        $application->setCatchExceptions(true);
        $application->setAutoExit(false);

        if (getenv('APPLICATION_ENV') == 'production') {
            $events = new EventDispatcher();
            $events->addListener(ConsoleEvents::ERROR, array($container->get('sentry_client'), 'logConsoleErrorEvent'));

            $application->setDispatcher($events);

            $errorHandler = new Raven_ErrorHandler($container->get('raven_client'));
            $errorHandler->registerErrorHandler()
                ->registerExceptionHandler()
                ->registerShutdownFunction();
        }

        $this->addCommands($application, $container);

        return $application;
    }

    /**
     * @param  Application        $application
     * @param  ContainerInterface $container
     * @return void
     */
    private function addCommands(Application $application, ContainerInterface $container)
    {
        $config = $container->get('config');
        $config = $config['litus']['console'];

        $commands = array();

        foreach ($config as $name => $invokable) {
            $container->setInvokableClass('console_' . $name, $invokable);

            $command = $container->get('console_' . $name);
            if ($command instanceof ServiceLocatorAwareInterface) {
                $command->setServiceLocator($container);
            }

            $commands[$name] = $command;
        }

        $application->addCommands(array_values($commands));
    }
}
