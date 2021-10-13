<?php

namespace CudiBundle\Command\Socket;

use CudiBundle\Component\Socket\Sale as SaleSocket;
use React\EventLoop\LoopInterface;

/**
 * Sale
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Sale extends \CommonBundle\Component\Console\Command\Socket
{
    protected function getCommandName()
    {
        return 'sale';
    }

    protected function getSocket(LoopInterface $loop)
    {
        return new SaleSocket(
            $this->getServiceLocator(),
            $loop,
            $this
        );
    }

    protected function getSocketUri()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_socket_file');
    }

    protected function isSocketEnabled()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_socket_enabled');
    }
}
