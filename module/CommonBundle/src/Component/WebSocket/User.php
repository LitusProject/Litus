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
 * This is the user who is connected to the websocket.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class User
{
	private $socket;
	
	private $handshaked = false;
	
	private $bufferType;
	private $buffer;
	
	const MAX_BUFFER_SIZE = 1048576;
	
	/**
	 * @param mixed $socket
	 */
	public function __construct($socket)
	{
		$this->socket = $socket;
	}
	
	/**
	 * @return mixed
	 */
	public function getSocket()
	{
		return $this->socket;
	}
	
	/**
	 * @return boolean
	 */
	public function hasHandshaked()
	{
		return $this->handshaked;
	}
	
	/**
	 * Do the handshake
	 *
	 * @param string $data
	 */
	public function doHandshake($data)
	{
		if ($this->hasHandshaked())
			return;
		
		$requestHeaders = array();

		foreach(explode("\r\n", $data) as $line) {
			@list($k, $v) = explode(':', $line, 2);
			$requestHeaders[$k] = ltrim($v);
		}

		if (empty($requestHeaders['Sec-WebSocket-Key'])) {
			return;
		}
		
		$key = base64_encode(sha1($requestHeaders['Sec-WebSocket-Key'] . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true));

		$response = "HTTP/1.1 101 Switching Protocols\r\n"
			. "Upgrade: WebSocket\r\n"
			. "Connection: Upgrade\r\n"
			. "Sec-WebSocket-Accept: " . $key . "\r\n"
			. "\r\n";

		if ($this->write($response))
			$this->handshaked = true;	
	}
	
	/**
	 * Write data to the user
	 *
	 * @param mixed $data
	 */
	public function write($data)
	{
		return socket_write($this->socket, $data, strlen($data));
	}
	
	/**
	 * Create the buffer for fragmented frames
	 *
	 * @param mixed $frame
	 */
	public function createBuffer($frame)
	{
		$this->bufferType = $frame['opcode'];
		$this->buffer = $frame['data'];
	}
	
	/**
	 * Append data to the buffer for fragmented frames
	 *
	 * @param mixed $frame
	 */
	public function appendBuffer($frame)
	{
		$this->buffer .= $frame['data'];
		
		if (strlen($this->buffer) > self::MAX_BUFFER_SIZE)
			$this->clearBuffer();
	}
	
	/**
	 * Clear the buffer of this user
	 */
	public function clearBuffer()
	{
		$this->buffer = '';
		$this->bufferType = false;
	}
	
	/**
	 * Return the complete message of the user
	 * 
	 * @return mixed
	 */
	public function getBuffer()
	{
		return $this->buffer;
	}
	
	/**
	 * Return the message type of the fragmented frames
	 * 
	 * @return integer
	 */
	public function getBufferType()
	{
		return $this->bufferType;
	}
}