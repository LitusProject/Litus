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

namespace CommonBundle\Component\Amon;

use CommonBundle\Component\Amon\Data\Exception as ExceptionData,
    CommonBundle\Component\Amon\Data\Log as LogData,
    CommonBundle\Component\Controller\Exception\HasNoAccessException,
    Exception,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent;

/**
 * Amon is server monitoring software that also has the ability to store an application's log
 * messages and exceptions. This client provides all functions needed to store Litus exceptions.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Client
{
    /**
     * @var \CommonBundle\Compnent\Amon\Connection $_connection The connection to the Amon server
     */
    private $_connection;

    /**
     * Constructs a new Amon client.
     *
     * @param \CommonBundle\Compnent\Amon\Connection $connection The connection to the Amon server
     */
    public function __construct(Connection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Sends a log to the server.
     *
     * @param string $message The message that should be sent
     * @param string $tags The tags associated with the message
     * @return void
     */
    public function sendLog($message, $tags = '')
    {
        $this->_connection->send(
            new LogData($message, $tags)
        );
    }

    /**
     * Logs an exception to the server.
     *
     * @param  \Exception $exception The exception that should be sent
     * @return void
     */
    public function sendException(Exception $exception)
    {
        $this->_connection->send(
            new ExceptionData($exception)
        );
    }

    /**
     * Handler that can be attached to Zend's EventManager and extracts the exception
     * from an MvcEvent
     *
     * @param \Zend\Mvc\MvcEvent $e The MvcEvent passed by the EventManager
     * @return void
     */
    public function handleMvcEvent(MvcEvent $e)
    {
        if ($e->getError() == Application::ERROR_EXCEPTION) {
            if (!($e->getParam('exception') instanceof HasNoAccessException))
                $this->sendException($e->getParam('exception'));
        }
    }
}
