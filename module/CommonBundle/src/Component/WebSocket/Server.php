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

namespace CommonBundle\Component\WebSocket;

use Exception;

/**
 * This is the server to handle all requests by the websocket protocol.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Server
{

    private $_address;
    private $_port;

    private $_users;
    private $_sockets;

    const OP_CONT = 0x0;
    const OP_TEXT = 0x1;
    const OP_BIN = 0x2;
    const OP_CLOSE = 0x8;
    const OP_PING = 0x9;
    const OP_PONG = 0xa;

    /**
     * @param string $address The url for the websocket master socket
     * @param integer $port The port to listen on
     */
    public function __construct($address, $port)
    {
        $this->_address = $address;
        $this->_port = $port;
        $this->_users = array();
        $this->_sockets = array();

        $this->createSocket();
    }

    /**
     * Create the master socket
     */
    private function createSocket()
    {
        $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$this->master)
            throw new Exception('Socket could not be created: ' . socket_last_error());

        $this->_sockets[] = $this->master;

        if (!socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1))
            throw new Exception('Socket options could not be set: ' . socket_last_error());

        if (!socket_bind($this->master, $this->_address, $this->_port))
            throw new Exception('Socket could not be binded to given address: ' . socket_last_error());

        if (!socket_listen($this->master, 20))
            throw new Exception('Could not listen to socket: ' . socket_last_error());
    }

    /**
     * Start listening on master socket and user sockets
     */
    public function process()
    {
        while(true){
            $changed = $this->_sockets;
            socket_select($changed, $write, $except, null);

            foreach($changed as $socket){
                if ($socket == $this->master) {
                    $this->_addUserSocket(socket_accept($this->master));
                } else {
                    $bytes = @socket_recv($socket, $buffer, 2048, 0);
                    if ($bytes == 0) {
                        $this->_removeUserSocket($socket);
                    } else {
                        $user = $this->getUserBySocket($socket);
                        if ($user->hasHandshaked()) {
                            $this->_processFrame($user, $buffer);
                        } else {
                            $user->doHandShake($buffer);
                            if ($user->hasHandshaked())
                                $this->onConnect($user);
                        }
                    }
                }
            }
        }
    }

    /**
     * Add a user socket to listen to
     *
     * @param mixed $socket
     */
    private function _addUserSocket($socket)
    {
        if (!$socket)
            return;
        $this->_users[] = new User($socket);
        $this->_sockets[] = $socket;
    }

    /**
     * Get a user by his socket
     *
     * @param mixed $socket
     * @return \CommonBundle\Component\WebSocket\User
     */
    public function getUserBySocket($socket)
    {
        foreach($this->_users as $user) {
            if ($user->getSocket() == $socket)
                return $user;
        }
    }

    /**
     * Remove a user socket
     *
     * @param mixed $socket
     */
    private function _removeUserSocket($socket)
    {
        foreach($this->_users as $key => $value) {
            if ($value->getSocket() == $socket) {
                unset($this->_users[$key]);
                $this->onClose($value, 0, '');
            }
        }

        @socket_close($socket);

        foreach($this->_sockets as $key => $value) {
            if ($value == $socket)
                unset($this->_sockets[$key]);
        }
    }

    /**
     * Remove a user
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     */
    protected function removeUser(User $user)
    {
        $this->_removeUserSocket($user->getSocket());
    }

    /**
     * Process a frame send by a user to the master socket
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     * @param mixed $data
     */
    private function _processFrame(User $user, $data)
    {
        $f = new Frame($data);

        if ($f->getIsFin() && $f->getOpcode() != 0) {
            if ($f->getIsControl()) {
                $this->_handleControlFrame($user, $f);
            } else {
                $this->handleDataFrame($user, $f);
            }
        } else if (!$f->getIsFin() && $f->getOpcode() != 0) {
            $user->createBuffer($f);
        } else if (!$f->getIsFin() && $f->getOpcode() == 0) {
            $user->appendBuffer($f);
        } else if ($f->getIsFin() && $f->getOpcode() == 0) {
            $user->appendBuffer($f);

            $this->handleDataFrame($user, $user->getBuffer());

            $user->clearBuffer();
        }
    }

    /**
     * Handle the received control frames
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     * @param \CommonBundle\Component\WebSocket\Frame $frame
     */
    private function _handleControlFrame(User $user, Frame $frame)
    {
        $len = strlen($frame->getData());

        if ($frame->getOpcode() == self::OP_CLOSE) {
            if ($len !== 0 && $len === 1)
                return;

            $statusCode = false;
            $reason = false;

            if ($len >= 2) {
                $unpacked = unpack('n', substr($frame->getData(), 0, 2));
                $statusCode = $unpacked[1];
                $reason = substr($frame->getData(), 3);
            }

            $user->write(chr(0x88) . chr(0));

            $this->_removeUserSocket($user->getSocket());
            $this->onClose($user, $statusCode, $reason);
        }
    }

    /**
     * Handle a received data frame
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     * @param \CommonBundle\Component\WebSocket\Frame $frame
     */
    protected function handleDataFrame(User $user, Frame $frame)
    {
        if ($frame->getOpcode() == self::OP_TEXT) {
            $this->gotText($user, $frame->getData());
        } else if ($frame->getOpcode() == self::OP_BIN) {
            $this->gotBin($user, $frame->getData());
        }
    }

    /**
     * Send text to a user socket
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     * @param string $text
     */
    public function sendText($user, $text)
    {
        $len = strlen($text);

        if ($len > 0xffff)
            return;

        $header = chr(0x81);

        if ($len >= 126) {
            $header .= chr(126) . pack('n', $len);
        } else {
            $header .= chr($len);
        }

        $user->write($header . $text);
    }

    /**
     * Send text to all user socket
     *
     * @param string $text
     */
    public function sendTextToAll($text)
    {
        $len = strlen($text);

        if ($len > 0xffff)
            return;

        $header = chr(0x81);

        if ($len >= 126) {
            $header .= chr(126) . pack('n', $len);
        } else {
            $header .= chr($len);
        }

        foreach($this->_users as $user)
            $user->write($header . $text);
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        return $this->_users;
    }

    /**
     * Parse received text
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     * @param string $data
     */
    protected function gotText(User $user, $data)
    {
    }

    /**
     * Parse received binary
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     * @param mixed $data
     */
    protected function gotBin(User $user, $data)
    {
    }

    /**
     * Do action when user closed his socket
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     * @param integer $statusCode
     * @param string $reason
     */
    protected function onClose(User $user, $statusCode, $reason)
    {
        $this->_removeUserSocket($user->getSocket());
    }

    /**
     * Do action when a new user has connected to this socket
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     */
    protected function onConnect(User $user)
    {
    }
}
