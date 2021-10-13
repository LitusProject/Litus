<?php

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
    public static function factory($uri, LoopInterface $loop)
    {
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
