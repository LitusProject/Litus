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

namespace CommonBundle\Component\Util;

use Doctrine\ORM\EntityManager;

/**
 * A utility class for websocket handling
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class WebSocket
{
    /**
     * Tries to kill the websocket with the given name.
     *
     * @param  EntityManager $em   The entity manager
     * @param  string        $name The socket to kill, one of
     *                             cudi:sale-queue
     *                             sport:run-queue
     *                             syllabus:update
     * @return object        An object with 'status' set to 'success' or 'error'
     *                            If 'error', 'reason' gives a reason and 'output' gives the
     *                            output of the failed command
     */
    public static function kill(EntityManager $em, $name)
    {
        $pidDir = $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('socket_path');

        $pidFile = $pidDir . '/pids' . $name . '.pid';

        $output = array();
        $return = 0;

        $pid = exec('cat ' . escapeshellarg($pidFile) . ' 2>&1', $output, $return);

        if (0 !== $return) {
            return (object) array(
                'status' => 'error',
                'reason' => 'pid_file',
                'output' => implode("\n", $output),
            );
        }

        $output = array();

        exec('kill ' . escapeshellarg($pid) . ' 2>&1', $output, $return);

        if (0 !== $return) {
            return (object) array(
                'status' => 'error',
                'reason' => 'kill_failed',
                'output' => implode("\n", $output),
            );
        }

        return (object) array(
            'status' => 'success',
        );
    }
}
