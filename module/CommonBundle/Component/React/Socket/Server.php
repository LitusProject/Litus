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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\React\Socket;

use League\Uri\Parser;
use React\EventLoop\LoopInterface;
use React\Socket\Server as Reactor;

class Server
{
    /**
     * @param  string        $uri  The address or URI to receive sockets on
     * @param  LoopInterface $loop The React loop to run the socket on
     * @return Server
     */
    public static function factory($uri, LoopInterface $loop) {
        $parser = new Parser();
        if ($parser($uri)['scheme'] == 'unix') {
            if (file_exists($parser($uri)['path'])) {
                unlink($parser($uri)['path']);
            }
        }

        $reactor = new Reactor($uri, $loop);

        if ($parser($uri)['scheme'] == 'unix') {
            chmod($parser($uri)['path'], 0777);
        }

        return $reactor;
    }
}
