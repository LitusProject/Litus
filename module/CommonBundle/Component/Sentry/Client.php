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

namespace CommonBundle\Component\Sentry;

use CommonBundle\Component\Authentication\Authentication;
use Exception;
use Laminas\Mvc\MvcEvent;
use Raven_Client;
use Symfony\Component\Console\Event\ConsoleErrorEvent;

/**
 * Sentry is an open-source error tracking platform that provides complete app logic,
 * deep context, and visibility across the entire stack in real time. This client
 * provides all functions needed to store Litus exceptions.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Client
{
    /**
     * @var Authentication The authentication instance
     */
    private $authentication;

    /**
     * @var Raven_Client The Raven client connecting to the Sentry server
     */
    private $ravenClient;

    /**
     * Constructs a new Sentry client.
     *
     * @param Raven_Client        $ravenClient    The Raven client connecting to the Sentry server
     * @param Authentication|null $authentication The authentication instance
     */
    public function __construct(Raven_Client $ravenClient, Authentication $authentication = null)
    {
        $this->authentication = $authentication;
        $this->ravenClient = $ravenClient;
    }

    /**
     * Sends an exception to the server.
     *
     * @param  Exception $exception The exception that should be sent
     * @return void
     */
    public function logException(\Throwable $exception)
    {
        $this->ravenClient->captureException(
            $exception,
            array(
                'user' => $this->getUser(),
            )
        );
    }

    /**
     * Sends a log message to the server.
     *
     * @param  string $message The message that should be sent
     * @return void
     */
    public function logMessage($message)
    {
        $this->ravenClient->captureMessage(
            $message,
            array(),
            array(
                'level' => 'info',
            )
        );
    }

    /**
     * Handler that can be attached to Symfony's EventDispatcher and extracts the
     * exception from a ConsoleErrorEvent.
     *
     * @param  ConsoleErrorEvent $event The ConsoleErrorEvent passed by the EventManager
     * @return void
     */
    public function logConsoleErrorEvent(ConsoleErrorEvent $event)
    {
        $this->logException(
            $event->getError()
        );
    }

    /**
     * Handler that can be attached to Zend's EventManager and extracts the
     * exception from an MvcEvent.
     *
     * @param  MvcEvent $event The MvcEvent passed by the EventManager
     * @return void
     */
    public function logMvcEvent(MvcEvent $event)
    {
        $exception = $event->getParam('exception');
        if (!$exception instanceof \Throwable) {
            return;
        }

        $this->logException($exception);
    }

    /**
     * Get the user.
     *
     * @return array
     */
    private function getUser()
    {
        if ($this->authentication->isAuthenticated()) {
            $user = array(
                'id'      => $this->authentication->getPersonObject()->getId(),
                'session' => $this->authentication->getSessionObject()->getId(),
            );
        } else {
            $user = array(
                'id'      => 0,
                'session' => '',
            );
        }

        return $user;
    }
}
