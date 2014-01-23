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

    /**
     * @return boolean
     */
    public function getIsFin()
    {
        return $this->_isFin;
    }

    /**
     * @return integer
     */
    public function getOpcode()
    {
        return $this->_opcode;
    }

    /**
     * @return boolean
     */
    public function getIsControl()
    {
        return $this->_isControl;
    }

    /**
     * @return boolean
     */
    public function getIsMasked()
    {
        return $this->_isMasked;
    }

    /**
     * @return integer
     */
    public function getPaylen()
    {
        return $this->_paylen;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param string
     */
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
        $data = substr($frame, 0, 2);
        $frame = substr($frame, 2);
        $b1 = ord($data[0]);
        $b2 = ord($data[1]);

        $isFin = ($b1 & (1 << 7)) != 0;
        $opcode = $b1 & 0x0f;
        $isControl = ($b1 & (1 << 3)) != 0;

        $isMasked = ($b2 & (1 << 7)) != 0;
        $paylen = $b2 & 0x7f;

        if ($paylen == 126) {
            $data = substr($frame, 0, 2);
            $frame = substr($frame, 2);
            $unpacked = unpack('n', $data);
            $paylen = $unpacked[1];
        } else if ($paylen == 127) {
            $data = substr($frame, 0, 8);
            $frame = substr($frame, 8);
            return;
        }

        if ($paylen >= self::MAX_PAYLOAD_LEN)
            return;

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
