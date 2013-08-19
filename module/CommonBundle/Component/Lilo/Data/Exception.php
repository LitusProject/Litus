<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
     * @var \CommonBundle\Component\Authentication\Authentication $_authentication The authentication instance
     */
    private $_authentication;

    /**
     * @var array The correctly formatted data object
     */
    private $_data = array();

    /**
     * Construct a new Exception object.
     *
     * @param \Exception $exception The exception that should be formatted
     * @param \CommonBundle\Component\Authentication\Authentication $authentication The authentication instance
     */
    public function __construct(\Exception $exception, Authentication $authentication = null)
    {
        $this->_authentication = $authentication;
        $this->_data = $this->_formatException($exception);
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
     * Turns the exception object into a correctly formatted array.
     *
     * @param \Exception $exception The exception that should be formatted
     * @param \CommonBundle\Component\Authentication\Authentication $authentication The authentication instance
     * @return array
     */
    private function _formatException(\Exception $exception, Authentication $authentication = null)
    {
        $data = array(
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),

            'environment' => array(
                'person' => $this->_getAuthenticationPerson(),
                'session' => $this->_getAuthenticationSession(),
                'url' => $this->_formatUrl(),
                'userAgent' => $_SERVER['HTTP_USER_AGENT']
            )
        );

        if (null !== $exception->getPrevious())
            $data['previous'] = $this->_formatData($exception->getPrevious());

        return $data;
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

    /**
     * Returns the username of the authenticated person.
     *
     * @return string
     */
    private function _getAuthenticationPerson()
    {
        if (null !== $this->_authentication && null !== $this->_authentication->getPersonObject())
            return $this->_authentication->getPersonObject()->getUsername();

        return '';
    }

    /**
     * Returns the authentication session ID.
     *
     * @return string
     */
    private function _getAuthenticationSession()
    {
        if (null !== $this->_authentication && null !== $this->_authentication->getSessionObject())
            return $this->_authentication->getSessionObject()->getId();

        return '';
    }
}
