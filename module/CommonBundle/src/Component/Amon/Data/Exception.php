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

namespace CommonBundle\Component\Amon\Data;

/**
 * This class stores an exception in the right format for Amon to process.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Exception extends \CommonBundle\Component\Amon\Data
{
    /**
     * @var array The correctly formatted data object
     */
    private $_data = array();

    /**
     * Construct a new Exception object.
     *
     * @param \Exception $exception The exception that should be formatted
     */
    public function __construct(\Exception $exception)
    {
        $this->_data = array(
            'exception_class' => get_class($exception),
            'message' => $exception->getMessage(),
            'backtrace' => $this->_formatBacktrace($exception),
            'request' => $this->_formatRequest()
        );
    }

    /**
     * Encodes the data in a JSON object.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->_data);
    }

    /**
     * Formats the exception's backtrace nicely.
     *
     * @param \Exception $exception The exception which trace should be formatted
     * @return array
     */
    private function _formatBacktrace(\Exception $exception)
    {
        $backtrace = array(
            0 => $exception->getMessage()
        );
        foreach ($exception->getTrace() as $t) {
            if (!isset($t['file']))
                continue;

            $backtrace[] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;at ' . (isset($t['class']) ? $t['class'] . '.' : '') . $t['function'] . '(' . basename($t['file']) . ':' . $t['line'] . ')';
        }

        return $backtrace;
    }

    /**
     * Creates that has the request information.
     *
     * @return array
     */
    private function _formatRequest()
    {
        $protocol = ('' != $_SERVER['HTTPS'] || 'off' != $_SERVER['HTTPS']) ? 'https://' : 'http://';

        return array(
            'url' => '' != $_SERVER['HTTP_HOST'] ? $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '',
            'request_method' => $_SERVER['REQUEST_METHOD']
        );
    }
}
