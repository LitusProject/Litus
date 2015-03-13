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

namespace SyllabusBundle\Command\Socket;

/**
 * Update command.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
use SyllabusBundle\Component\WebSocket\Update as UpdateSocket;

class Update extends \CommonBundle\Component\Console\Command\WebSocket
{
    protected function createSocket()
    {
        return new UpdateSocket($this->getEntityManager(), $this->getMailTransport());
    }

    protected function getCommandName()
    {
        return 'update';
    }

    protected function isSocketEnabled()
    {
        return '1' === $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.update_socket_enabled');
    }
}
