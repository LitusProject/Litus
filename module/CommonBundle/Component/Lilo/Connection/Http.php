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

namespace CommonBundle\Component\Lilo\Connection;

use CommonBundle\Component\Lilo\Data,
    CommonBundle\Component\Lilo\Data\Exception as ExceptionData,
    Zend\Http\Client,
    Zend\Http\Request;

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
     * @var boolean The port used by the server
     */
    private $_secure = true;

    /**
     * @var string The application's secret key
     */
    private $_secretKey = '';

    /**
     * Creates a new HTTP connection.
     *
     * @param string $host      The application's host
     * @param bool   $secure    Whether or not to use a secure connection
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
     * @param  Data $data The data object that should be sent
     * @return void
     */
    public function send(Data $data)
    {
        $client = new Client($this->_getRequestUrl($data));
        $client->setMethod(Request::METHOD_POST)
            ->setParameterPost(
                array(
                    'data' => (string) $data,
                    'key' => $this->_secretKey,
                )
            );

        $client->send();
    }

    /**
     * Generates the request URL based on the data type.
     *
     * @param  Data   $data The data object
     * @return string
     */
    private function _getRequestUrl(Data $data)
    {
        return (true === $this->_secure ? 'https://' : 'http://')
            . $this->_host
            . '/api/'
            . ($data instanceof ExceptionData ? 'exception' : 'log') . '/add';
    }
}
