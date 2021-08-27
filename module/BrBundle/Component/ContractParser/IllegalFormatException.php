<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class IllegalFormatException extends \Exception
{
    /**
     * @var integer
     */
    private $lineNb;

    /**
     * @param  integer $lineNumber
     * @return null
     */
    public function setLineNumber($lineNumber)
    {
        $this->lineNb = $lineNumber;
    }

    /**
     * @return integer
     */
    public function getLineNumber()
    {
        return $this->lineNb;
    }
}
