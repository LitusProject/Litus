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

namespace CommonBundle\Component\Lilo\Connection;

use CommonBundle\Component\Lilo\Data,
    CommonBundle\Component\Lilo\Data\Exception as ExceptionData,
    CommonBundle\Component\Lilo\Data\Log as LogData,
    Exception,
    Zend\Http\Client,
    Zend\Http\Request,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent;

/**
 * This client provides all functions needed to store Litus exceptions.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Http extends \CommonBundle\Component\Lilo\Connection
{
    /**
     * @var string The server's address
     */
    private $_host = 'localhost';

    /**
     * @var integer The port used by the server
     */
    private $_secure = true;

    /**
     * @var string The application's secret key
     */
    private $_secretKey = '';

    /**
     * Creates a new HTTP connection.
     *
     * @param string $host The application's host
     * @param bool $secure Whether or not to use a secure connection
     * @param string $secretKey The application's secret key
     */
    public function __construct($host = '', $secure = true, $secretKey = '')
    {
        $this->_host = $host;
        $this->_secure = $secure;
        $this->_secretKey = $secretKey;
    }

    /**
     * Sends the given data object to the server.
     *
     * @param \CommonBundle\Component\Lilo\Data $data The data object that should be sent
     * @return void
     */
    public function send(Data $data)
    {
        $client = new Client($this->_getRequestUrl($data));
        $client->setMethod(Request::METHOD_POST)
            ->setRawBody((string) $data);

        $client->send();
    }

    /**
     * Generates the request URL based on the data type.
     *
     * @param \CommonBundle\Component\Lilo\Data $data The data object
     * @return string
     */
    private function _getRequestUrl(Data $data)
    {
        return (true === $this->_secure ? 'https://' : 'http://')
            . $this->_host
            . '/api/'
            . ($data instanceof ExceptionData ? 'exception' : 'log') . '/';
    }
}
