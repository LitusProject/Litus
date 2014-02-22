<?php

namespace CommonBundle\Component\Console;

use Zend\ServiceManager\ServiceLocatorInterface,
    Symfony\Component\Console\Helper\HelperSet,
    Symfony\Component\Console\Application;

class ApplicationFactory extends \DoctrineModule\Service\CliFactory
{
    /**
     * {@inheritDoc}
     * @return \Symfony\Component\Console\Application
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $cli = new Application;
        $cli->setName('Litus Command Line Interface');
        $cli->setVersion('0.1');
        $cli->setHelperSet(new HelperSet);
        $cli->setCatchExceptions(true);
        $cli->setAutoExit(false);

        // Load commands using event
        $this->getEventManager($sl)->trigger('loadCli.post', $cli, array('ServiceManager' => $sl));

        return $cli;
    }
}
