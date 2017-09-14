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

namespace CommonBundle\Component\WebSocket;

/**
 * This is the frame send over the websocket.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Frame
{
    private $isFin;
    private $opcode;
    private $isControl;
    private $isMasked;
    private $paylen;
    private $data;

    const MAX_BUFFER_SIZE = 1048576;
    const MAX_PAYLOAD_LEN = 1048576;

    /**
     * @param string $data
     */
    public function __construct($data)
    {
        $this->decodeFrame($data);
    }

    /**
     * @return boolean
     */
    public function getIsFin()
    {
        return $this->isFin;
    }

    /**
     * @return integer
     */
    public function getOpcode()
    {
        return $this->opcode;
    }

    /**
     * @return boolean
     */
    public function getIsControl()
    {
        return $this->isControl;
    }

    /**
     * @return boolean
     */
    public function getIsMasked()
    {
        return $this->isMasked;
    }

    /**
     * @return integer
     */
    public function getPaylen()
    {
        return $this->paylen;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string
     * @param string $data
     */
    public function appendData($data)
    {
        $this->data .= $data;

        if (strlen($this->data) > self::MAX_BUFFER_SIZE) {
            $this->data = '';
        }
    }

    /**
     * Decode the received frame
     *
     * @param string $frame
     */
    private function decodeFrame($frame)
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
        } elseif ($paylen == 127) {
            return;
        }

        if ($paylen >= self::MAX_PAYLOAD_LEN) {
            return;
        }

        $data = '';

        if ($isMasked) {
            $mask = substr($frame, 0, 4);
            $frame = substr($frame, 4);

            if ($paylen) {
                $data = substr($frame, 0, $paylen);

                for ($i = 0, $j = 0, $l = strlen($data); $i < $l; $i++) {
                    $data[$i] = chr(ord($data[$i]) ^ ord($mask[$j]));

                    if ($j++ >= 3) {
                        $j = 0;
                    }
                }
            }
        } elseif ($paylen) {
            $data = substr($frame, 0, $paylen);
        }

        $this->isFin = $isFin;
        $this->opcode = $opcode;
        $this->isControl = $isControl;
        $this->isMasked = $isMasked;
        $this->paylen = $paylen;
        $this->data = $data;
    }
}
