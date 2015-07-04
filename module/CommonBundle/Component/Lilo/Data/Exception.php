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

use CommonBundle\Component\Authentication\Authentication,
    Exception as GenericException,
    Zend\Console\Request as ConsoleRequest,
    Zend\Http\PhpEnvironment\Request as PhpRequest,
    Zend\Stdlib\RequestInterface;

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
    private $data = array();

    /**
     * @var RequestInterface The request to the page
     */
    private $request;

    /**
     * Construct a new Exception object.
     *
     * @param \Exception       $exception      The exception that should be formatted
     * @param Authentication   $authentication The authentication instance
     * @param RequestInterface $request        The request to the page
     */
    public function __construct(GenericException $exception, Authentication $authentication, RequestInterface $request)
    {
        $this->request = $request;
        $this->data = array(
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'trace' => $this->formatBacktrace($exception),
            'environment' => array(
                'person' => $authentication->isAuthenticated()
                    ? $authentication->getPersonObject()->getFullName() . ' (' . $authentication->getPersonObject()->getUsername() . ')'
                    : 'Guest',
                'session' => $authentication->isAuthenticated()
                    ? $authentication->getSessionObject()->getId()
                    : '',
                'url' => $this->formatUrl(),
                'userAgent' => $this->getUserAgent(),
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
        return json_encode($this->data);
    }

    /**
     * Formats the exception's backtrace nicely.
     *
     * @param  \Exception $exception The exception which trace should be formatted
     * @return array
     */
    private function formatBacktrace(GenericException $exception)
    {
        $backtrace = array();
        foreach ($exception->getTrace() as $t) {
            if (!isset($t['file'])) {
                continue;
            }

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
    private function formatUrl()
    {
        if ($this->request instanceof ConsoleRequest) {
            return $this->request->toString();
        } elseif ($this->request instanceof PhpRequest) {
            return '' != $this->request->getServer()->get('HTTP_HOST')
                ? (($this->request->getServer()->get('HTTPS') != 'off') ? 'https://' : 'http://') . $this->request->getServer()->get('HTTP_HOST') . $this->request->getServer()->get('REQUEST_URI')
                : '';
        }

        return '';
    }

    /**
     * Get the user agent
     *
     * @return string
     */
    private function getUserAgent()
    {
        if ($this->request instanceof ConsoleRequest) {
            return 'Console';
        } elseif ($this->request instanceof PhpRequest) {
            return $this->request->getServer()->get('HTTP_USER_AGENT');
        }

        return '';
    }
}
