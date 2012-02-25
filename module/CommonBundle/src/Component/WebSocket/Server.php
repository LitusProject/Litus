<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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

	private $address;
	private $port;
	
	private $users;
	private $sockets;

	const MAX_PAYLOAD_LEN = 1048576;
	
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
		$this->address = $address;
		$this->port = $port;
		$this->users = array();
		$this->sockets = array();
				
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
		
		$this->sockets[] = $this->master;
		
		if (!socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1))
			throw new Exception('Socket options could not be set: ' . socket_last_error());
		
		if (!socket_bind($this->master, $this->address, $this->port))
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
			$changed = $this->sockets;
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
		$this->users[] = new User($socket);
		$this->sockets[] = $socket;
	}
	
	/**
	 * Get a user by his socket
	 *
	 * @param mixed $socket
	 * @return \CommonBundle\Component\WebSockets\User
	 */
	public function getUserBySocket($socket)
	{
		foreach($this->users as $user) {
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
		foreach($this->users as $key => $value) {
			if ($value->getSocket() == $socket)
				unset($this->users[$key]);
		}
		
		foreach($this->sockets as $key => $value) {
			if ($value == $socket)
				unset($this->sockets[$key]);
		}
	}
	
	/**
	 * Process a frame send by a user to the master socket
	 *
	 * @param \CommonBundle\Component\WebSockets\User $user
	 * @param mixed $frame
	 */
	private function _processFrame(User $user, $frame)
	{
		$f = $this->_decodeFrame($frame);
		
		/* unfragmented message */
		if ($f['isFin'] && $f['opcode'] != 0) {
			/* unfragmented messages may represent a control frame */
			if ($f['isControl']) {
				$this->_handleControlFrame($user, $f['opcode'], $f['data']);
			} else {
				$this->handleDataFrame($user, $f['opcode'], $f['data']);
			}
		}
		/* start fragmented message */
		else if (!$f['isFin'] && $f['opcode'] != 0) {
			$user->createBuffer($f);
		}
		/* continue fragmented message */
		else if (!$f['isFin'] && $f['opcode'] == 0) {
			$user->appendBuffer($f);
		}
		/* finalize fragmented message */
		else if ($f['isFin'] && $f['opcode'] == 0) {
			$user->appendBuffer($f);
			
			$this->handleDataFrame($user, $user->getBufferType(), $user->getBuffer());
			
			$user->clearBuffer();
		}
	}
	
	/**
	 * Decode the received frame
	 *
	 * @param mixed $frame
	 */
	private function _decodeFrame($frame)
	{
		/* read first 2 bytes */
		$data = substr($frame, 0, 2);
		$frame = substr($frame, 2);
		$b1 = ord($data[0]);
		$b2 = ord($data[1]);
		
		/* Bit 0 of Byte 1: Indicates that this is the final fragment in a
		 * message.  The first fragment MAY also be the final fragment.*/
		$isFin = ($b1 & (1 << 7)) != 0;
		/* Bits 4-7 of Byte 1: Defines the interpretation of the payload data. */
		$opcode = $b1 & 0x0f;
		/* Control frames are identified by opcodes where the most significant
		 * bit of the opcode is 1 */
		$isControl = ($b1 & (1 << 3)) != 0;
		/* Bit 0 of Byte 2: If set to 1, a masking key is present in
		 * masking-key, and this is used to unmask the payload data. */
		$isMasked = ($b2 & (1 << 7)) != 0;
		/* Bits 1-7 of Byte 2: The length of the payload data. */
		$paylen = $b2 & 0x7f;
		
		/* read extended payload length, if applicable */
		
		if ($paylen == 126) {
			/* the following 2 bytes are the actual payload len */
			$data = substr($frame, 0, 2);
			$frame = substr($frame, 2);
			$unpacked = unpack('n', $data);
			$paylen = $unpacked[1];
		} else if ($paylen == 127) {
			/* the following 8 bytes are the actual payload len */
			$data = substr($frame, 0, 8);
			$frame = substr($frame, 8);
			return;
		}
		
		if ($paylen >= self::MAX_PAYLOAD_LEN)
			return;
		
		/* read masking key and decode payload data */
		
		$mask = false;
		$data = '';
		
		if ($isMasked) {
			$mask = substr($frame, 0, 4);
			$frame = substr($frame, 4);
		
			if ($paylen) {
				$data = substr($frame, 0, $paylen);
				$frame = substr($frame, $paylen);
			
				for ($i = 0, $j = 0, $l = strlen($data); $i < $l; $i++) {
					$data[$i] = chr(ord($data[$i]) ^ ord($mask[$j]));
				
					if ($j++ >= 3) {
						$j = 0;
					}
				}
			}
		} else if ($paylen) {
			$data = substr($frame, 0, $paylen);
			$frame = substr($frame, $paylen);
		}
		
		$decoded['isFin'] = $isFin;
		$decoded['opcode'] = $opcode;
		$decoded['isControl'] = $isControl;
		$decoded['isMasked'] = $isMasked;
		$decoded['paylen'] = $paylen;
		$decoded['data'] = $data;
		
		return $decoded;
	}
	
	/**
	 * Handle the received control frames
	 *
	 * @param \CommonBundle\Component\WebSockets\User $user
	 * @param integer $type
	 * @param mixed $data
	 */
	private function _handleControlFrame(User $user, $type, $data)
	{
		$len = strlen($data);
		
		if ($type == self::OP_CLOSE) {
			/* If there is a body, the first two bytes of the body MUST be a
			 * 2-byte unsigned integer */
			if ($len !== 0 && $len === 1) {
				return;
			}
			
			$statusCode = false;
			$reason = false;
			
			if ($len >= 2) {
				$unpacked = unpack('n', substr($data, 0, 2));
				$statusCode = $unpacked[1];
				$reason = substr($data, 3);
			}
						
			/* Send close frame.
			* 0x88: 10001000 fin, opcode close */
			$user->write(chr(0x88) . chr(0));
			
			$this->onClose($user, $statusCode, $reason);
			$this->_removeUserSocket($user->getSocket());
		}
	}
	
	/**
	 * Handle a received data frame
	 *
	 * @param \CommonBundle\Component\WebSockets\User $user
	 * @param integer $type
	 * @param mixed $data
	 */
	protected function handleDataFrame(User $user, $type, $data)
	{
		if ($type == self::OP_TEXT) {
			$this->gotText($user, $data);
		} else if ($type == self::OP_BIN) {
			$this->gotBin($user, $data);
		}
	}
	
	/**
	 * Send text to a user socket
	 *
	 * @param \CommonBundle\Component\WebSockets\User $user
	 * @param string $text 
	 */
	public function sendText($user, $text)
	{
		$len = strlen($text);
		
		/* extended 64bit payload not implemented yet */
		if ($len > 0xffff) {
			return;
		}
		
		/* 0x81 = first and last bit set (fin, opcode=text) */
		$header = chr(0x81);
		
		/* extended 32bit payload */
		if ($len >= 125) {
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
		
		/* extended 64bit payload not implemented yet */
		if ($len > 0xffff) {
			return;
		}
		
		/* 0x81 = first and last bit set (fin, opcode=text) */
		$header = chr(0x81);
		
		/* extended 32bit payload */
		if ($len >= 125) {
			$header .= chr(126) . pack('n', $len);
		} else {
			$header .= chr($len);
		}
		
		foreach($this->users as $user)
			$user->write($header . $text);
	}
	
	/**
	 * Parse received text
	 *
	 * @param \CommonBundle\Component\WebSockets\User $user
	 * @param string $data
	 */
	protected function gotText(User $user, $data)
	{
	}
	
	/**
	 * Parse received binary
	 *
	 * @param \CommonBundle\Component\WebSockets\User $user
	 * @param mixed $data
	 */
	protected function gotBin(User $user, $data)
	{
	}
	
	/**
	 * Do action when user closed his socket
	 *
	 * @param \CommonBundle\Component\WebSockets\User $user
	 * @param integer $statusCode
	 * @param string $reason
	 */
	protected function onClose(User $user, $statusCode, $reason)
	{
	}
	
	/**
	 * Do action when a new user has connected to this socket
	 *
	 * @param \CommonBundle\Component\WebSockets\User $user
	 */
	protected function onConnect(User $user)
	{
	}
}