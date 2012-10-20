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

namespace CommonBundle\Component\Amon\Connection;

use CommonBundle\Component\Amon\Data,
    CommonBundle\Component\Amon\Data\Exception as ExceptionData,
    CommonBundle\Component\Amon\Data\Log as LogData,
    Exception,
    Zend\Http\Client,
    Zend\Http\Request,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent;

/**
 * Amon is server monitoring software that also has the ability to store .
 * This client provides all functions needed to store Litus exceptions.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Http extends \CommonBundle\Component\Amon\Connection
{
    /**
     * @var string The server's address
     */
    private $_server = '127.0.0.1';

    /**
     * @var integer The port used by the server
     */
    private $_port = 2464;

    /**
     * @var string The application's secret key
     */
    private $_secretKey = '';

    /**
     * Creates a new HTTP connection.
     *
     * @param string $server The server's address
     * @param integer $port The port used by the server
     * @param string $secretKey The application's secret key
     */
    public function __construct($server = '', $port = '', $secretKey = '')
    {
        $this->_server = $server;
        $this->_port = $port;
        $this->_secretKey = $secretKey;
    }

    /**
     * Sends the given data object to the server.
     *
     * @param \CommonBundle\Component\Amon\Data $data The data object that should be sent
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
     * @param \CommonBundle\Component\Amon\Data $data The data object
     * @return string
     */
    private function _getRequestUrl(Data $data)
    {
        $url = 'http://' . $this->_server . ':' . $this->_port . '/api/';

        if ($data instanceof ExceptionData) {
            $url .= 'exception/';
        } else if ($data instanceof LogData) {
            $url .= 'log/';
        }

        $url .= $this->_secretKey;

        return $url;
    }
}
