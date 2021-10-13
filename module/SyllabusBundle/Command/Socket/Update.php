<?php

namespace SyllabusBundle\Command\Socket;

use React\EventLoop\LoopInterface;
use SyllabusBundle\Component\Socket\Update as UpdateSocket;

/**
 * Update
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Update extends \CommonBundle\Component\Console\Command\Socket
{
    protected function getCommandName()
    {
        return 'update';
    }

    protected function getSocket(LoopInterface $loop)
    {
        return new UpdateSocket(
            $this->getServiceLocator(),
            $loop,
            $this
        );
    }

    protected function getSocketUri()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.update_socket_file');
    }

    protected function isSocketEnabled()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.update_socket_enabled');
    }
}
