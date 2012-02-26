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
 * This is the frame send over the websocket.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Frame
{
	private $_isFin;
	private $_opcode;
	private $_isControl;
	private $_isMasked;
	private $_paylen;
	private $_data;
	
	const MAX_BUFFER_SIZE = 1048576;
	const MAX_PAYLOAD_LEN = 1048576;

	/**
	 * @param mixed $data
	 */
	public function __construct($data)
	{
		$this->_decodeFrame($data);
	}
	
	public function getIsFin()
	{
		return $this->_isFin;
	}
	
	public function getOpcode()
	{
		return $this->_opcode;
	}
	
	public function getIsControl()
	{
		return $this->_isControl;
	}
	
	public function getIsMasked()
	{
		return $this->_isMasked;
	}
	
	public function getPaylen()
	{
		return $this->_paylen;
	}
	
	public function getData()
	{
		return $this->_data;
	}
	
	public function appendData($data)
	{
		$this->_data .= $data;
		
		if (strlen($this->_data) > self::MAX_BUFFER_SIZE)
			$this->_data = '';
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
		
		$this->_isFin = $isFin;
		$this->_opcode = $opcode;
		$this->_isControl = $isControl;
		$this->_isMasked = $isMasked;
		$this->_paylen = $paylen;
		$this->_data = $data;
	}
}