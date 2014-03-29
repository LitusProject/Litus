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

namespace SportBundle\Command\Socket;

use SportBundle\Component\WebSocket\Run\Queue as RunQueueSocket;

/**
 * RunQueue socket
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class RunQueue extends \CommonBundle\Component\Console\Command\WebSocket
{
    protected function createSocket()
    {
        return new RunQueueSocket($this->getEntityManager());
    }

    protected function getCommandName()
    {
        return 'run-queue';
    }
}
