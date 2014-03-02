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

namespace CommonBundle\Component\Lilo\Data;

use CommonBundle\Component\Authentication\Authentication;

/**
 * This class converts an exception to the right format for the
 * Lilo API.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Exception extends \CommonBundle\Component\Lilo\Data
{
    /**
     * @var array The correctly formatted data object
     */
    private $_data = array();

    /**
     * Construct a new Exception object.
     *
     * @param \Exception                                            $exception      The exception that should be formatted
     * @param \CommonBundle\Component\Authentication\Authentication $authentication The authentication instance
     */
    public function __construct(\Exception $exception, Authentication $authentication)
    {
        $this->_data = array(
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'trace' => $this->_formatBacktrace($exception),
            'environment' => array(
                'person' => $authentication->isAuthenticated()
                    ? $authentication->getPersonObject()->getFullName() . ' ('. $authentication->getPersonObject()->getUsername() . ')'
                    : 'Guest',
                'session' => $authentication->isAuthenticated()
                    ? $authentication->getSessionObject()->getId()
                    : '',
                'url' => $this->_formatUrl(),
                'userAgent' => $_SERVER['HTTP_USER_AGENT'],
            ),
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
     * @param  \Exception $exception The exception which trace should be formatted
     * @return array
     */
    private function _formatBacktrace(\Exception $exception)
    {
        $backtrace = array();
        foreach ($exception->getTrace() as $t) {
            if (!isset($t['file']))
                continue;

            $backtrace[] = array(
                'file' => basename($t['file']),
                'line' => $t['line'],
                'class' => isset($t['class']) ? $t['class'] : '',
                'function' => $t['function'],
                'args' => '',
            );
        }

        return $backtrace;
    }

    /**
     * Formats the request URL.
     *
     * @return string
     */
    private function _formatUrl()
    {
        return '' != $_SERVER['HTTP_HOST']
            ? ((isset($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
            : '';
    }
}
