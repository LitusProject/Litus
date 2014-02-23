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
