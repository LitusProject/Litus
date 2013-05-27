<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Lilo;

/**
 * This class represents a connection to Lilo.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Connection
{
    /**
     * Sends the given data object to the server.
     *
     * @param \CommonBundle\Component\Lilo\Data $data The data object that should be sent
     * @return void
     */
    abstract public function send(Data $data);
}
